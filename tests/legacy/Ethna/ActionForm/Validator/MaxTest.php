<?php
/*
 * Copyright (C) the Ethna contributors. All rights reserved.
 *
 * This file is part of the Ethna package, distributed under new BSD.
 * For full terms see the included LICENSE file.
 */

class Ethna_ActionForm_Validator_MaxTest extends PHPUnit_Framework_TestCase
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

    public function test_Validate_Max_Integer()
    {
        $form_def = array(
            'type' => VAR_TYPE_INT,
            'form_type' => FORM_TYPE_TEXT,
            'required' => true,
            'max' => 5,
        );
        $this->af->setDef('input', $form_def);

        $this->af->set('input', 5);
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 6);
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 4);
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
    }

    public function test_Validate_Max_Float()
    {
        $form_def = array(
            'type' => VAR_TYPE_FLOAT,
            'form_type' => FORM_TYPE_TEXT,
            'required' => true,
            'max' => 5,
        );
        $this->af->setDef('input', $form_def);

        $this->af->set('input', 4.999999);
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 5.000001);
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 5.0);
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 6.0);
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
    }

    public function test_Validate_Max_DateTime()
    {
        $form_def = array(
            'type' => VAR_TYPE_DATETIME,
            'form_type' => FORM_TYPE_TEXT,
            'required' => true,
            'max' => '2000-01-01',
        );
        $this->af->setDef('input', $form_def);

        $this->af->set('input', '1999-12-31');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', '2000-01-02');
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', '2000-01-01');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();
    }

    public function test_Validate_Max_String_UTF8()
    {
        $form_def = array(
            'type' => VAR_TYPE_STRING,
            'form_type' => FORM_TYPE_TEXT,
            'required' => true,
            'max' => 5,
        );
        $this->af->setDef('input', $form_def);

        //   in ascii.
        $this->af->set('input', 'abcd');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 'abcdef');
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 'abcde');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        //   multibyte.
        $this->af->set('input', '???????????????');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', '??????????????????');
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', '????????????');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
    }

    public function test_Validate_Max_String_EUCJP()
    {
        $this->controller->setClientEncoding('EUC-JP');

        $form_def = array(
            'type' => VAR_TYPE_STRING,
            'form_type' => FORM_TYPE_TEXT,
            'required' => true,
            'max' => 4,  //  ??????2???????????????4??????
        );
        $this->af->setDef('input', $form_def);

        //   in ascii.
        $this->af->set('input', 'abc');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 'abcde');
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 'abcd');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        //   multibyte.
        $this->af->set('input', mb_convert_encoding('??????', 'EUC-JP', 'UTF-8'));
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', mb_convert_encoding('?????????', 'EUC-JP', 'UTF-8'));
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', mb_convert_encoding('???', 'EUC-JP', 'UTF-8'));
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));

        //   reset client encoding
        $this->controller->setClientEncoding('UTF-8');
    }

    public function test_Validate_Max_String_ASCII()
    {
        $this->controller->setClientEncoding('ASCII');

        $form_def = array(
            'type' => VAR_TYPE_STRING,
            'form_type' => FORM_TYPE_TEXT,
            'required' => true,
            'max' => 4,  //  ascii 4??????
        );
        $this->af->setDef('input', $form_def);

        //   in ascii.
        $this->af->set('input', 'abc');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 'abcde');
        $this->af->validate();
        $this->assertTrue($this->ae->isError('input'));
        $this->ae->clear();

        $this->af->set('input', 'abcd');
        $this->af->validate();
        $this->assertFalse($this->ae->isError('input'));
        $this->ae->clear();

        //   reset client encoding
        $this->controller->setClientEncoding('UTF-8');
    }

//    function test_Validate_Max_File()
//    {
//        //  skipped because we can't bypass
//        //  is_uploaded_file function.
//    }

}

