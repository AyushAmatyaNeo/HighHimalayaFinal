<?php
namespace Setup\Form;


use Zend\Form\Annotation;
/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("performanceForm")
 */
class PerformanceForm
{
    /**
    * @Annotation\Type("Zend\Form\Element\Text")
    * @Annotation\Required({"required":"true"})
    * @Annotation\Options({"disable_inarray_validator":"true","label":"Index Id"})
    * @Annotation\Attributes({ "id":"performanceId","class":"indexId form-control","data-init-plugin":"select2","tabindex":"-1"})
    */
    public $indexId;

    /**
    * @Annotation\Type("Zend\Form\Element\Number")
    * @Annotation\Required({"required":"true"})
    * @Annotation\Options({"label":"Percent Range"})
    * @Annotation\Attributes({ "id":"percentRange", "class":"percentRange form-control", "min":"0", "step":"0.01" })
    */
    public $percentRange;
   
    /**
    * @Annotation\Type("Zend\Form\Element\Text")
    * @Annotation\Options({"label":"Percent Description"})
    * @Annotation\Attributes({ "id":"percentDescription", "class":"percentDescription form-control" })
    */
    public $percentDesc;

    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"id":"submit","value":"Submit","class":"submit"})
     */
    public $submit;

}