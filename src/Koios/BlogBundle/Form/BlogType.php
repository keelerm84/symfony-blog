<?php

namespace Koios\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BlogType extends AbstractType {
    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('title', 'text')
            ->add('blog', 'ckeditor')
            ->add('tags', 'text');
    }

    public function getName() {
        return 'koios_blogbundle_blogtype';
    }
}
