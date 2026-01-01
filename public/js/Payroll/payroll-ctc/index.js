(function ($) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        const $employeeId = $('#employeeId');
        const $fiscalYear = $('#fiscalYear');
        const $employeeName = $('#employeeName'); // Cache employee name element
        const $fiscalYearName = $('#fiscalYearName');

        // Button for exporting Excel
        $('#exportExcel').on('click', function () {
            const employeeName = $employeeName.text() || 'Employee'; // Fallback to 'Employee' if name is empty
            const fiscalYearName = $fiscalYearName.text() || 'Fiscal Year';
            const filename = `Monthly Payroll CTC of ${employeeName} for ${fiscalYearName}.xlsx`;
            exportTableToExcel('salary-table', filename);
        });

        // Button for exporting PDF
        $('#exportPdf').on('click', function () {
            const employeeName = $employeeName.text() || 'Employee'; // Fallback to 'Employee' if name is empty
            const fiscalYearName = $fiscalYearName.text() || 'Fiscal Year';
            const filename = `Monthly Payroll CTC of ${employeeName} for ${fiscalYearName}.pdf`;
            exportTableToPDF('salary-table', filename);
        });

        $('#search').on('click', function () {
            app.pullDataById('', {
                'employeeId': $employeeId.val(),
                'fiscalYear': $fiscalYear.val(),
            }).then(function (response) {
                if (response.success && response.data.length > 0) {
                    const maritalCode = response.data[0].MARITAL_STATUS;
                    const isMarried = maritalCode && maritalCode.toUpperCase() === 'M';
                    const employeeName = response.data[0].FULL_NAME || '';
                    $('#employeeName').text(employeeName); // Update employee name

                    const fiscalYearName = $fiscalYear.find('option:selected').text();
                    $('#fiscalYearName').text(fiscalYearName);

                    const ssfPf = response.data[0].SSF_PF;

                    renderSalaryAdditionsOnly(response.data, isMarried, ssfPf);
                } else {
                    app.showMessage(response.error || 'No data found.', 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        function computeNepalTDS(annualIncome, isMarried, ssfPf) {
            let slabs, rates;

            // Define slabs based on marital status
            slabs = isMarried
                ? [600000, 800000, 1100000, 2000000, 5000000]  // Married slabs
                : [500000, 700000, 1000000, 2000000, 5000000];  // Unmarried slabs

            // Tax rates corresponding to each slab
            rates = [0.01, 0.10, 0.20, 0.30, 0.36, 0.39];

            // If SSF and income is below first slab, no TDS should be calculated
            if (ssfPf === 'SSF' && annualIncome < slabs[0]) {
                return 0;
            }

            let remaining = annualIncome;
            let totalTax = 0;
            let prev = 0;

            // Loop through the slabs and calculate tax for each slab
            for (let i = 0; i < slabs.length; i++) {
                const slabRange = slabs[i] - prev;
                const taxable = Math.min(slabRange, remaining);
                totalTax += taxable * rates[i];  // Add tax for the current slab
                remaining -= taxable;
                prev = slabs[i];

                if (remaining <= 0) break;
            }

            // If PF (and not SSF), subtract the first slab tax from the total tax
            let firstSlabTax = 0;
            firstSlabTax = Math.min(slabs[0], annualIncome) * rates[0];  // Calculate tax for the first slab
            // console.log(firstSlabTax);
            // Subtract the first slab's tax from the total tax for SSF
            if (ssfPf == 'SSF') {
                totalTax -= firstSlabTax;
            }

            return totalTax;
        }

        function renderSalaryAdditionsOnly(data, isMarried = false, ssfPf) {
            const tableBody = document.querySelector('#salary-table tbody');
            tableBody.innerHTML = '';

            let basicSalary = 0;

            // First pass to find Basic Salary
            data.forEach(item => {
                const desc = item.NAME;
                if (desc === 'Basic Salary') {
                    basicSalary = parseFloat(item.FLAT_VALUE || 0);
                }
            });

            const additionRows = [];
            const deductionRows = [];
            let totalAddition = 0;
            let totalDeduction = 0;

            // Second pass: prepare rows and sum up additions and deductions
            data.forEach(item => {
                const desc = item.NAME;
                let value = 0;

                if (item.VALUE !== null && item.VALUE !== undefined) {
                    value = basicSalary * (parseFloat(item.VALUE) / 100);
                } else if (item.FLAT_VALUE !== null && item.FLAT_VALUE !== undefined) {
                    value = parseFloat(item.FLAT_VALUE);
                }

                value = parseFloat(value.toFixed(2));

                // Filter logic based on ssfPf value
                if (ssfPf === 'None') {
                    // If ssfPf is 'None', exclude rows with SSF or PF
                    if (!desc.includes("SSF") && !desc.includes("PF")) {
                        if (item.TYPE === 'A' && desc && value > 0) {
                            additionRows.push({ desc, value });
                            totalAddition += value;
                        } else if (item.TYPE === 'D' && desc && value > 0) {
                            deductionRows.push({ desc, value });
                            totalDeduction += value;
                        }
                    }
                } else if (ssfPf === 'PF') {
                    // If ssfPf is PF, exclude SSF-related rows
                    if (!desc.includes("SSF")) {
                        if (item.TYPE === 'A' && desc && value > 0) {
                            additionRows.push({ desc, value });
                            totalAddition += value;
                        } else if (item.TYPE === 'D' && desc && value > 0) {
                            deductionRows.push({ desc, value });
                            totalDeduction += value;
                        }
                    }
                } else if (ssfPf === 'SSF') {
                    // If ssfPf is SSF, exclude PF-related rows
                    if (!desc.includes("PF")) {
                        if (item.TYPE === 'A' && desc && value > 0) {
                            additionRows.push({ desc, value });
                            totalAddition += value;
                        } else if (item.TYPE === 'D' && desc && value > 0) {
                            deductionRows.push({ desc, value });
                            totalDeduction += value;
                        }
                    }
                }
            });

            const A = totalDeduction * 12;
            const B = (basicSalary * 1 / 3) * 12;
            const C = 500000;

            // Find the smallest value between A, B, and C
            const smallest = Math.min(A, B, C);

            const adjustedAddition = totalAddition * 12;
            const annualIncome = adjustedAddition - smallest;

            // Pass ssfPf (SSP/PF) to the TDS function
            const tdsAnnual = computeNepalTDS(annualIncome, isMarried, ssfPf);
            const tdsMonthly = parseFloat((tdsAnnual / 12).toFixed(2));

            deductionRows.push({ desc: 'TDS/SST', value: tdsMonthly });
            totalDeduction += tdsMonthly;

            const maxLength = Math.max(additionRows.length, deductionRows.length);

            while (additionRows.length < maxLength) additionRows.push({ desc: '', value: '' });
            while (deductionRows.length < maxLength) deductionRows.push({ desc: '', value: '' });

            for (let i = 0; i < maxLength; i++) {
                const a = additionRows[i];
                const d = deductionRows[i];
                const row = document.createElement('tr');
                row.innerHTML = ` 
                    <td>${a.desc || '&nbsp;'}</td>
                    <td>${a.value ? `Rs. ${a.value.toFixed(2)}` : '&nbsp;'}</td>
                    <td>${d.desc || '&nbsp;'}</td>
                    <td>${d.value ? `Rs. ${d.value.toFixed(2)}` : '&nbsp;'}</td>
                `;
                tableBody.appendChild(row);
            }

            const totalRow = document.createElement('tr');
            totalRow.innerHTML = ` 
                <td style="font-weight:bold">Total Addition (A)</td>
                <td style="font-weight:bold">Rs. ${totalAddition.toFixed(2)}</td>
                <td style="font-weight:bold">Total Deduction (B)</td>
                <td style="font-weight:bold">Rs. ${totalDeduction.toFixed(2)}</td>`;
            tableBody.appendChild(totalRow);

            const netRow = document.createElement('tr');
            netRow.innerHTML = ` 
                <td style="font-weight:bold; background:#f0f0f0;" colspan="3">Net Payable (A - B)</td>
                <td style="font-weight:bold; background:#f0f0f0;">Rs. ${(totalAddition - totalDeduction).toFixed(2)}</td>`;
            tableBody.appendChild(netRow);

            // Add row for Annual Income
            const annualIncomeRow = document.createElement('tr');
            annualIncomeRow.innerHTML = ` 
                <td style="font-weight:bold; background:#f9f9f9;" colspan="3">Annual Income</td>
                <td style="font-weight:bold; background:#f9f9f9;">Rs. ${annualIncome.toFixed(2)}</td>`;
            tableBody.appendChild(annualIncomeRow);

            // Add row for Total Tax (TDS)
            const totalTaxRow = document.createElement('tr');
            totalTaxRow.innerHTML = ` 
                <td style="font-weight:bold; background:#f9f9f9;" colspan="3">Total Annual Tax (TDS/SST)</td>
                <td style="font-weight:bold; background:#f9f9f9;">Rs. ${tdsAnnual.toFixed(2)}</td>`;
            tableBody.appendChild(totalTaxRow);
        }

        // Export table to Excel
        function exportTableToExcel(tableId, filename) {
            const table = document.getElementById(tableId);
            const wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
            XLSX.writeFile(wb, filename);
        }

        // Export table to PDF using html2pdf.js
        function exportTableToPDF(tableId, filename) {
            const element = document.getElementById(tableId);

            // Use html2pdf.js to convert the table to PDF
            const opt = {
                margin:       1,
                filename:     filename,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().from(element).set(opt).save();
        }

    });
})(window.jQuery);
