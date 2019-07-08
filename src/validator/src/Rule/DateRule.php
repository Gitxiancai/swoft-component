<?php declare(strict_types=1);

namespace Swoft\Validator\Rule;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\Date;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class IsDateRule
 *
 * @since 2.0
 *
 * @Bean(Date::class)
 */
class DateRule implements RuleInterface
{
    /**
     * @param array $data
     * @param string $propertyName
     * @param object $item
     * @param null $default
     *
     * @return array
     * @throws ValidatorException
     */
    public function validate(array $data, string $propertyName, $item, $default = null): array
    {
        $value = $data[$propertyName];
        if (strtotime($value)) {
            return $data;
        }

        /* @var Date $item */
        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s must date!', $propertyName) : $message;
        throw new ValidatorException($message);
    }

}
