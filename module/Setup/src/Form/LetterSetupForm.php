<?php

namespace Setup\Form;

use Zend\Form\Annotation;



/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("LetterSetupForm")
 */
class LetterSetupForm
{


  /**
   * @Annotation\Type("Zend\Form\Element\Text")
   * @Annotation\Required({"required":"true"})
   * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
   * @Annotation\Options({"label":"Letter Title"})
   * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
   * @Annotation\Attributes({ "id":"form-letter-title", "class":"form-letter-title form-control" })
   */
  public $letterTitle;

  /**
   * @Annotation\Type("Zend\Form\Element\Textarea")
   * @Annotation\Required(false)
   * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
   * @Annotation\Options({"label":"Type In Nepali"})
   * @Annotation\Attributes({
   *     "id":"language-textarea",
   *     "rows":"8",
   *     "cols":"120",
   *     "style":"width: 100%; display: none; font-size: 16px; padding: 10px;",
   *     "placeholder":"Enter Nepali Text here..."
   * })
   */

  public $nepaliText;
  /**
   * @Annotation\Type("Zend\Form\Element\Submit")
   * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
   */



  public $submit;
}
