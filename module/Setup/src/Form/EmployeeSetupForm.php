<?php
namespace Setup\Form;


use Zend\Form\Annotation;
/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("employeeSetupForm")
 */
class EmployeeSetupForm
{
   
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"DNM Employee"})
     * @Annotation\Attributes({ "id":"employeeCode", "class":"employeeCode form-control" })
     */
    public $employeeCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Hris Employee"})
     * @Annotation\Attributes({ "id":"employeeId","class":"employeeId form-control","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $employeeId;

    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"id":"submit","value":"Submit","class":"submit"})
     */
    public $submit;

}