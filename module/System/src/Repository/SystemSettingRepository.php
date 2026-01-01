<?php

namespace System\Repository;

use Application\Model\Model;
use Application\Model\Preference;
use Zend\Db\Adapter\AdapterInterface;
use Psr\Log\LoggerInterface;

class SystemSettingRepository {

    private $adapter;
    private $logger;

    public function __construct(AdapterInterface $adapter, LoggerInterface $logger = null) {
        $this->adapter = $adapter;
        $this->logger = $logger;
    }

    /**
     * Validate an identifier (KEY name) — allow only uppercase letters, numbers and underscores.
     * Prevents SQL injection via identifiers used in the PIVOT clause.
     */
    private function validateIdentifier($ident) {
        return (bool) preg_match('/^[A-Z0-9_]+$/', $ident);
    }

    public function fetch() {
        $preference = new Preference();
        $mappings = $preference->mappings; // trusted source inside app

        // Build the IN list of identifiers safely after validation.
        $inParts = [];
        foreach ($mappings as $k => $v) {
            $upper = strtoupper($v);
            if (!$this->validateIdentifier($upper)) {
                // skip or throw — do not include unsafe identifiers
                if ($this->logger) $this->logger->warning("Skipping invalid preference key: {$v}");
                continue;
            }
            // Use quoted identifier form: "KEY_NAME" AS KEY_NAME — Oracle accepts unquoted uppercase identifiers.
            $inParts[] = "'{$upper}' AS {$upper}";
        }

        if (empty($inParts)) {
            // nothing valid — return empty result to avoid executing bad SQL
            return [];
        }

        $valuesinCSV = implode(',', $inParts);

        $sql = "SELECT *
                FROM
                  (SELECT KEY, VALUE FROM HRIS_PREFERENCES) PIVOT ( MAX(VALUE) FOR KEY IN ({$valuesinCSV}) )";

        // The SQL contains only validated identifiers now; execute and return first row
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $rows = iterator_to_array($result, false);
        return $rows[0] ?? [];
    }

    public function edit(Model $model) {
        // Use MERGE to upsert safely with bound parameters.
        // MERGE INTO HRIS_PREFERENCES t
        // USING (SELECT :key AS KEY, :val AS VALUE FROM dual) s
        // ON (t.KEY = s.KEY)
        // WHEN MATCHED THEN UPDATE SET t.VALUE = s.VALUE
        // WHEN NOT MATCHED THEN INSERT (KEY, VALUE) VALUES (s.KEY, s.VALUE);

        $sql = "MERGE INTO HRIS_PREFERENCES t
                USING (SELECT :p_key AS KEY, :p_val AS VALUE FROM DUAL) s
                ON (t.KEY = s.KEY)
                WHEN MATCHED THEN
                  UPDATE SET t.VALUE = s.VALUE
                WHEN NOT MATCHED THEN
                  INSERT (KEY, VALUE) VALUES (s.KEY, s.VALUE)";

        // Prepare the statement once, reuse for multiple keys
        $statement = $this->adapter->createStatement($sql);

        foreach ($model->mappings as $key => $prefKey) {
            // Validate identifier (prefKey) — it should be a known preference key
            $upperKey = strtoupper($prefKey);
            if (!$this->validateIdentifier($upperKey)) {
                if ($this->logger) $this->logger->warning("Skipping invalid preference key: {$prefKey}");
                continue;
            }

            $value = $model->{$key};

            // If required, sanitize or limit length of value (for example to 4000 bytes)
            if (is_string($value) && strlen($value) > 4000) {
                $value = substr($value, 0, 4000);
            }

            $params = [
                'p_key' => $upperKey,
                'p_val' => (string)$value,
            ];

            try {
                $statement->execute($params);
            } catch (\Exception $e) {
                // log the error and continue or rethrow depending on your policy
                if ($this->logger) {
                    $this->logger->error("Failed to upsert preference {$upperKey}", [
                        'exception' => $e->getMessage()
                    ]);
                } else {
                    error_log("Failed to upsert preference {$upperKey}: " . $e->getMessage());
                }
                // optionally rethrow or continue to next key
                throw $e;
            }
        }
    }
}