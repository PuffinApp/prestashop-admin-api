<?php

namespace Ps_borest\Classes\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class AbstractControllerWrapper extends AbstractController {
    public function _createFormBuilder($form, $options = []): FormBuilderInterface {
        return $this->createFormBuilder($form, $options);
    }
}