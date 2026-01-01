<?php
namespace Setup\Form;


use Zend\Form\Annotation;
/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("performanceSetupForm")
 */
class PerformanceSetupForm
{
    /**
    * @Annotation\Type("Zend\Form\Element\Text")
    * @Annotation\Required({"required":"true"})
    * @Annotation\Options({"disable_inarray_validator":"true","label":"Performance Id"})
    * @Annotation\Attributes({ "id":"performanceId","class":"performanceId form-control","data-init-plugin":"select2","tabindex":"-1"})
    */
    public $performanceId;
   
    /**
    * @Annotation\Type("Zend\Form\Element\Text")
    * @Annotation\Options({"label":"Category Name"})
    * @Annotation\Attributes({ "id":"categoryName", "class":"categoryName form-control" })
    */
    public $categoryName;

    /**
    * @Annotation\Type("Zend\Form\Element\Number")
    * @Annotation\Required({"required":"true"})
    * @Annotation\Options({"label":"Credit"})
    * @Annotation\Attributes({ "id":"credit", "class":"credit form-control", "min":"0", "step":"0.01" })
    */
    public $credit;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"id":"submit","value":"Submit","class":"submit"})
     */
    public $submit;

}