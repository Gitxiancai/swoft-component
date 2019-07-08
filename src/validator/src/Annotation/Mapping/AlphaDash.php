<?php declare(strict_types=1);

namespace Swoft\Validator\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class AlphaDash
 *
 * @since 2.0
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Attributes({
 *      @Attribute("message",type="string")
 * })
 */
class AlphaDash
{
    /**
     * @var string
     */
    private $message = '';

    /**
     * AlphaDash constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['message'])) {
            $this->message = $values['message'];
        }
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
