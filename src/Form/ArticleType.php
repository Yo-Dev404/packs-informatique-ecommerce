<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
// use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('category', EntityType::class, [
               'class' => Category::class,
               'choice_label' => 'title' 
            ])
            ->add('content')
            ->add('image')
            //->add('images', FileType::class, [
               // 'classe' => Image::class,
                //'label' => false,
                //'mapped' => false,
                //'required' => false
          //  ])
    
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
