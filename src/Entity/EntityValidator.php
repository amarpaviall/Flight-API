<?php

declare(strict_types=1);

namespace App\Entity;

use App\Http\Error\Exception\ValidationException;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidator
{
    public function __construct(
        private ValidatorInterface $validator
    ){

    }

    public function validate(EntityInterface $entity, ServerRequestInterface $request,
       array $groups = []){
     //dd('validate method!');
     $errors = $this->validator->validate(value: $entity, groups: $groups);

    // dd($errors);

     if(count($errors) === 0){
        return;
     }

     $validationErrors = [];

     foreach($errors as $error){
        $validationErrors[] = [
            "property" => $error->getPropertyPath(),
            "message" => $error->getMessage()
        ];
     }
    
     //dd($validationErrors);
     
     $validationException = new ValidationException($request);

     $validationException->setErrors($validationErrors);

     //dd($validationException->getErrors());

     throw $validationException;

    }
}