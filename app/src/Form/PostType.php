<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Заголовок'])
            ->add('body', TextareaType::class, ['label' => 'Текст поста'])
            ->add('slug', TextType::class, ['label' => 'Посилання на пост'])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'mapped' => false,
                'choice_label' => 'name',
                'label' => "Прив'язати до категорії",

            ])
            ->add('image', FileType::class, ['label' => 'Додати зображення'])
            ->add('save', SubmitType::class, ['label' => 'Зберегти'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
