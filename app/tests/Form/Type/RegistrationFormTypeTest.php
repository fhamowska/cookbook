<?php

/*
 * Registration form type test.
 */

namespace App\Tests\Form\Type;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;

/**
 * RegistrationFormTypeTest class.
 */
class RegistrationFormTypeTest extends TypeTestCase
{
    /**
     * Test that form has email and password fields.
     */
    public function testFormFieldsExist(): void
    {
        $form = $this->factory->create(RegistrationFormType::class);

        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('password'));
    }

    /**
     * Test password field is not mapped to entity.
     */
    public function testPasswordFieldIsNotMapped(): void
    {
        $form = $this->factory->create(RegistrationFormType::class);

        $passwordConfig = $form->get('password')->getConfig();
        $this->assertFalse($passwordConfig->getMapped());
    }

    /**
     * Test submitting valid data binds email and ignores password mapping.
     */
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

    /**
     * Test password field has NotBlank and Length constraints.
     */
    public function testPasswordConstraints(): void
    {
        $form = $this->factory->create(RegistrationFormType::class);

        $passwordField = $form->get('password');

        $constraints = $passwordField->getConfig()->getOption('constraints');

        $this->assertContainsOnlyInstancesOf(\Symfony\Component\Validator\Constraint::class, $constraints);

        $this->assertContainsEquals(new NotBlank([]), $constraints);

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

    /**
     * Test submitting invalid password triggers validation errors.
     */
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
    }

    /**
     * Add validator extension to form.
     *
     * @return ValidatorExtension
     */
    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }
}
