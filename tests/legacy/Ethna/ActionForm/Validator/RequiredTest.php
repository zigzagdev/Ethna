<?php
/*
 * Copyright (C) the Ethna contributors. All rights reserved.
 *
 * This file is part of the Ethna package, distributed under new BSD.
 * For full terms see the included LICENSE file.
 */

class Ethna_ActionForm_Validator_RequiredTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->controller = new Ethna_Controller_Dummy();
        $this->ae = $this->controller->getActionError();
        $this->af = new Ethna_ActionForm_Dummy($this->controller);
        $this->controller->setActionForm($this->af);

        $this->af->use_validator_plugin = false;
        $this->af->clearFormVars();
        $this->af->setDef(null, array());
        $this->ae->clear();
    }

    public function test_Validate_Required_Integer()
    {
        $form_def = array(
            'type' => VAR_TYPE_INT,
            'form_type' => FORM_TYPE_TEXT,
            'required' => true,
        );
        $this->af->setDef('input', $form_def);

        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 5);
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', '0');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', null);
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', '');
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
    }

    public function test_Validate_Required_Float()
    {
        $form_def = array(
            'type' => VAR_TYPE_FLOAT,
            'form_type' => FORM_TYPE_TEXT,
            'required' => true,
        );
        $this->af->setDef('input', $form_def);

        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 4.999999);
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', null);
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', '');
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
    }

    public function test_Validate_Required_DateTime()
    {
        $form_def = array(
            'type' => VAR_TYPE_DATETIME,
            'form_type' => FORM_TYPE_TEXT,
            'required' => true,
        );
        $this->af->setDef('input', $form_def);

        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', '1999-12-31');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', null);
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', '');
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
    }

    public function test_Validate_Min_String()
    {
        $form_def = array(
            'type' => VAR_TYPE_STRING,
            'form_type' => FORM_TYPE_TEXT,
            'required' => true,
        );
        $this->af->setDef('input', $form_def);

        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', '????????????');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 'abcd');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', null);
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', '');
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
    }

//    function test_Validate_Required_File()
//    {
//        //  skipped because we can't bypass
//        //  is_uploaded_file function.
//    }

    public function test_Validate_Required_Integer_Array()
    {
        $test_form_type = array(
            FORM_TYPE_TEXT,
            FORM_TYPE_PASSWORD,
            FORM_TYPE_TEXTAREA,
            FORM_TYPE_SELECT,
            FORM_TYPE_RADIO,
            FORM_TYPE_CHECKBOX,
            FORM_TYPE_BUTTON,
            FORM_TYPE_HIDDEN,
        );

        //
        //    FILE???????????????????????????????????????????????????
        //
        foreach ($test_form_type as $form_type) {

            $form_def = array(
                'type' => array(VAR_TYPE_INT),
                'form_type' => $form_type,
                'required' => true,
            );
            $this->af->setDef('input', $form_def);

            //   Form?????????submit??????????????????????????????
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            //   ???????????????, ????????????????????????????????????????????????????????????????????????????????????
            $this->af->set('input', array(5, null, null));
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            $this->af->set('input', array(5, 6, 7));
            $this->af->validate();
            $this->assertFalse($this->ae->isError('input'));
            $this->ae->clear();

            //   ???????????????????????????
            $this->af->set('input', array());
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            //   required_num ????????????????????????
            //   ????????????????????????valid?????????????????????????????????????????????
            $form_def = array(
                'type' => array(VAR_TYPE_INT),
                'form_type' => $form_type,
                'required' => true,
                'required_num' => 2,
            );
            $this->af->setDef('input', $form_def);

            $this->af->set('input', array(5, 6));
            $this->af->validate();
            $this->assertFalse($this->ae->isError('input'));
            $this->ae->clear();

            $this->af->set('input', array(5, null));
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            //   required_key ????????????????????????
            //   ????????????????????????????????????valid?????????????????????????????????????????????
            $form_def = array(
                'type' => array(VAR_TYPE_INT),
                'form_type' => $form_type,
                'required' => true,
                'required_key' => array(1),
            );
            $this->af->setDef('input', $form_def);

            $this->af->set('input', array(null, 6));
            $this->af->validate();
            $this->assertFalse($this->ae->isError('input'));
            $this->ae->clear();

            $this->af->set('input', array(6, null));
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();
        }
    }

    public function test_Validate_Required_Float_Array()
    {
        $test_form_type = array(
            FORM_TYPE_TEXT,
            FORM_TYPE_PASSWORD,
            FORM_TYPE_TEXTAREA,
            FORM_TYPE_SELECT,
            FORM_TYPE_RADIO,
            FORM_TYPE_CHECKBOX,
            FORM_TYPE_BUTTON,
            FORM_TYPE_HIDDEN,
        );

        //
        //    FILE???????????????????????????????????????????????????
        //
        foreach ($test_form_type as $form_type) {

            $form_def = array(
                'type' => array(VAR_TYPE_FLOAT),
                'form_type' => $form_type,
                'required' => true,
            );
            $this->af->setDef('input', $form_def);

            //   Form?????????submit??????????????????????????????
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            //   ???????????????, ????????????????????????????????????????????????????????????????????????????????????
            $this->af->set('input', array(5.0, null, null));
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            $this->af->set('input', array(5.1, 6.65, 91.099));
            $this->af->validate();
            $this->assertFalse($this->ae->isError('input'));
            $this->ae->clear();

            //   ???????????????????????????
            $this->af->set('input', array());
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            //   required_num ????????????????????????
            //   ????????????????????????valid?????????????????????????????????????????????
            $form_def = array(
                'type' => array(VAR_TYPE_FLOAT),
                'form_type' => $form_type,
                'required' => true,
                'required_num' => 2,
            );
            $this->af->setDef('input', $form_def);

            $this->af->set('input', array(5.12, 87.090));
            $this->af->validate();
            $this->assertFalse($this->ae->isError('input'));
            $this->ae->clear();

            $this->af->set('input', array('abcd', 878.911));
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            //   required_key ????????????????????????
            //   ????????????????????????????????????valid?????????????????????????????????????????????
            $form_def = array(
                'type' => array(VAR_TYPE_FLOAT),
                'form_type' => $form_type,
                'required' => true,
                'required_key' => array(1),
            );
            $this->af->setDef('input', $form_def);

            $this->af->set('input', array(null, 6.13));
            $this->af->validate();
            $this->assertFalse($this->ae->isError('input'));
            $this->ae->clear();

            $this->af->set('input', array(6.019, 'abcd'));
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();
        }
    }

    public function test_Validate_Required_Datetime_Array()
    {
        $test_form_type = array(
            FORM_TYPE_TEXT,
            FORM_TYPE_PASSWORD,
            FORM_TYPE_TEXTAREA,
            FORM_TYPE_SELECT,
            FORM_TYPE_RADIO,
            FORM_TYPE_CHECKBOX,
            FORM_TYPE_BUTTON,
            FORM_TYPE_HIDDEN,
        );

        //
        //    FILE???????????????????????????????????????????????????
        //
        foreach ($test_form_type as $form_type) {

            $form_def = array(
                'type' => array(VAR_TYPE_DATETIME),
                'form_type' => $form_type,
                'required' => true,
            );
            $this->af->setDef('input', $form_def);

            //   Form?????????submit??????????????????????????????
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            //   ???????????????, ????????????????????????????????????????????????????????????????????????????????????
            $this->af->set('input', array('2005-01-01', '2005-01-44', null));
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            $this->af->set('input', array('2005-01-01', '2005-01-02', '2005-01-03'));
            $this->af->validate();
            $this->assertFalse($this->ae->isError('input'));
            $this->ae->clear();

            //   ???????????????????????????
            $this->af->set('input', array());
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            //   required_num ????????????????????????
            //   ????????????????????????valid?????????????????????????????????????????????
            $form_def = array(
                'type' => array(VAR_TYPE_DATETIME),
                'form_type' => $form_type,
                'required' => true,
                'required_num' => 2,
            );
            $this->af->setDef('input', $form_def);

            $this->af->set('input', array('2008-01-01', '2008-01-02'));
            $this->af->validate();
            $this->assertFalse($this->ae->isError('input'));
            $this->ae->clear();

            $this->af->set('input', array('2008-01-02', 'abcd'));
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            //   required_key ????????????????????????
            //   ????????????????????????????????????valid?????????????????????????????????????????????
            $form_def = array(
                'type' => array(VAR_TYPE_DATETIME),
                'form_type' => $form_type,
                'required' => true,
                'required_key' => array(1),
            );
            $this->af->setDef('input', $form_def);

            $this->af->set('input', array(null, '2009-12-31'));
            $this->af->validate();
            $this->assertFalse($this->ae->isError('input'));
            $this->ae->clear();

            $this->af->set('input', array('2008-12-11', 'abcd'));
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();
        }
    }

    public function test_Validate_Required_String_Array()
    {
        $test_form_type = array(
            FORM_TYPE_TEXT,
            FORM_TYPE_PASSWORD,
            FORM_TYPE_TEXTAREA,
            FORM_TYPE_SELECT,
            FORM_TYPE_RADIO,
            FORM_TYPE_CHECKBOX,
            FORM_TYPE_BUTTON,
            FORM_TYPE_HIDDEN,
        );

        //
        //    FILE???????????????????????????????????????????????????
        //
        foreach ($test_form_type as $form_type) {

            $form_def = array(
                'type' => array(VAR_TYPE_STRING),
                'form_type' => $form_type,
                'required' => true,
            );
            $this->af->setDef('input', $form_def);

            //   Form?????????submit??????????????????????????????
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            //   ???????????????, ????????????????????????????????????????????????????????????????????????????????????
            $this->af->set('input', array("abcd", null, null));
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            $this->af->set('input', array("abcd", "cdef", "hogehoge"));
            $this->af->validate();
            $this->assertFalse($this->ae->isError('input'));
            $this->ae->clear();

            //   ???????????????????????????
            $this->af->set('input', array());
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            //   required_num ????????????????????????
            //   ????????????????????????valid?????????????????????????????????????????????
            $form_def = array(
                'type' => array(VAR_TYPE_STRING),
                'form_type' => $form_type,
                'required' => true,
                'required_num' => 2,
            );
            $this->af->setDef('input', $form_def);

            $this->af->set('input', array("abcd", "cdef"));
            $this->af->validate();
            $this->assertFalse($this->ae->isError('input'));
            $this->ae->clear();

            $this->af->set('input', array("abcd", null));
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();

            //   required_key ????????????????????????
            //   ????????????????????????????????????valid?????????????????????????????????????????????
            $form_def = array(
                'type' => array(VAR_TYPE_STRING),
                'form_type' => $form_type,
                'required' => true,
                'required_key' => array(1),
            );
            $this->af->setDef('input', $form_def);

            $this->af->set('input', array(null, "abcd"));
            $this->af->validate();
            $this->assertFalse($this->ae->isError('input'));
            $this->ae->clear();

            $this->af->set('input', array("abcd", null));
            $this->af->validate();
            $this->assertTrue($this->ae->isError('input'));
            $this->ae->clear();
        }
    }
}
