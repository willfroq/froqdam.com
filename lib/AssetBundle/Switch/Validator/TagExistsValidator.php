<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Validator;

use Froq\PortalBundle\Repository\TagRepository;
use Pimcore\Model\DataObject\Tag;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class TagExistsValidator extends ConstraintValidator
{
    public function __construct(private readonly TagRepository $tagRepository)
    {

    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        assert(assertion: $constraint instanceof TagExists);

        $data = json_decode($value, true);

        if (($data === null && json_last_error() !== JSON_ERROR_NONE) || !is_array($data)) {
            $this->context->buildViolation($constraint->message, ['$keyName' => (string) $data])
                ->addViolation();
        }

        $tags = $data;

        foreach ($tags as $tag) {
            $tagCode = $tag['code'] ?? '';

            $tag = $this->tagRepository->getTagByCode((string) $tagCode);

            if ($tag instanceof Tag) {
                $this->context->buildViolation($constraint->message, ['$tagCode' => $tagCode])
                    ->addViolation();
            }
        }
    }
}
