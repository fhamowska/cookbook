<?php

namespace App\Tests\Form\Type;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;

class RegistrationFormTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }

    public function testFormFieldsExist(): void
    {
        $form = $this->factory->create(RegistrationFormType::class);

        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('password'));
    }

    public function testPasswordFieldIsNotMapped(): void
    {
        $form = $this->factory->create(RegistrationFormType::class);

        $passwordConfig = $form->get('password')->getConfig();
        $this->assertFalse($passwordConfig->getMapped());
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'email' => 'user@example.com',
            'password' => 'strongpassword',
        ];

        $user = new User();

        $form = $this->factory->create(RegistrationFormType::class, $user);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals('user@example.com', $user->getEmail());

        $this->assertNull($user->getPassword());
    }

    public function testPasswordConstraints(): void
    {
        $form = $this->factory->create(RegistrationFormType::class);

        $passwordField = $form->get('password');

        $constraints = $passwordField->getConfig()->getOption('constraints');

        $this->assertContainsOnlyInstancesOf(\Symfony\Component\Validator\Constraint::class, $constraints);

        $this->assertContainsEquals(new NotBlank(['message' => 'Please enter a password']), $constraints);

        $lengthConstraint = null;
        foreach ($constraints as $constraint) {
            if ($constraint instanceof Length) {
                $lengthConstraint = $constraint;
                break;
            }
        }
        $this->assertNotNull($lengthConstraint);
        $this->assertEquals(6, $lengthConstraint->min);
        $this->assertEquals(4096, $lengthConstraint->max);
    }

    public function testSubmitInvalidPasswordTriggersValidationErrors(): void
    {
        $formData = [
            'email' => 'user@example.com',
            'password' => '',
        ];

        $form = $this->factory->create(RegistrationFormType::class);

        $form->submit($formData);

        $this->assertFalse($form->isValid());

        $errors = $form->get('password')->getErrors();
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('Please enter a password', $errors[0]->getMessage());
    }
}
