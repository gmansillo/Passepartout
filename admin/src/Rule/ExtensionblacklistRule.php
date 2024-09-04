<?php

namespace GiovanniMansillo\Component\Dory\Administrator\Rule;

use Joomla\CMS\Form\FormRule;
use Joomla\CMS\Form\Form;
use Joomla\Registry\Registry;

class ExtensionblacklistRule extends FormRule
{
    /**
     * Method to test the extension of a filename
     *
     * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
     * @param   mixed              $value    The form field value to validate.
     * @param   string             $group    The field name group control value.
     * @param   Registry           $input    An optional Registry object with the entire data set to validate against the entire form.
     * @param   Form               $form     The form object for which the field is being tested.
     */

    public function test(\SimpleXMLElement $element, $value, $group = null, Registry $input = null, Form $form = null)
    {
        return true;
        // TODO: Implement file extension check


        die("siam qui!" . $value);

        /* test 1 */
        if ($value < 1) {
            $element->attributes()->message = 'The value ' . $value . ' is not valid because it is less than 1';
            // The above line works if you have already specified a custom message by adding the message="..." attribute
            // to your form field. If you haven't then use instead
            // $element->addAttribute('message', 'The value ' . $value . ' is not valid because it is less than 1');
            return false;



            if (preg_match(\chr(1) . $this->regex . \chr(1) . $this->modifiers, $value)) {
                return true;
            }

            $attr = $element->attributes();
            $error_message = 'The telephone number ' . $value . ' is wrong';
            // how you write the message attribute to the XML element depends on whether it's already set
            if (isset($attr['message'])) {
                $element->attributes()->message = $error_message;
            } else {
                $element->addAttribute('message', $error_message);
            }
            return false;
        }

        /* test 2 */
        if ($value > 1000) {
            $element->attributes()->message = 'The value ' . $value . ' is not valid because it is greater than 1000';
            return false;
        }

        return true;
    }
}
