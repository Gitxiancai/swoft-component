<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2019-02-04
 * Time: 16:46
 */

namespace Swoft\WebSocket\Server\Annotation\Parser;

use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Annotation\AnnotationException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Stdlib\Helper\Str;
use Swoft\WebSocket\Server\Annotation\Mapping\WsModule;
use Swoft\WebSocket\Server\Router\RouteRegister;

/**
 * Class WebSocketParser
 * @since 2.0
 *
 * @AnnotationParser(WsModule::class)
 */
class WsModuleParser extends Parser
{
    /**
     * @var array
     */
    private static $modules = [];

    /**
     * @var array
     */
    private static $commands = [];

    /**
     * Parse object
     *
     * @param int      $type Class or Method or Property
     * @param WsModule $annotation Annotation object
     *
     * @return array
     * Return empty array is nothing to do!
     * When class type return [$beanName, $className, $scope, $alias, $size] is to inject bean
     * When property type return [$propertyValue, $isRef] is to reference value
     */
    public function parse(int $type, $annotation): array
    {
        if ($type !== self::TYPE_CLASS) {
            throw new AnnotationException('`@WsModule` must be defined on class!');
        }

        $class = $this->className;

        RouteRegister::bindModule($class, [
            'path'           => $annotation->getPath(),
            'name'           => $annotation->getName(),
            'class'          => $class,
            'eventMethods'   => [],
            'controllers'    => $annotation->getControllers(),
            'messageParser'  => $annotation->getMessageParser(),
            'defaultCommand' => $annotation->getDefaultCommand(),
        ]);

        return [$class, $class, Bean::SINGLETON, ''];
    }

    /**
     * @param string $moduleClass
     * @param string $method
     * @param string $event such as: message, handShake, open, close
     */
    public static function bindEvent(string $moduleClass, string $method, string $event): void
    {
        self::$modules[$moduleClass]['eventMethods'][$event] = $method;
    }

    /**
     * @param string $moduleClass
     * @param string $controllerClass
     * @param string $prefix
     */
    public static function bindController(string $moduleClass, string $controllerClass, string $prefix): void
    {
        self::$modules[$moduleClass]['controllers'][] = $controllerClass;

        self::$commands[$controllerClass] = [
            'prefix' => $prefix ?: Str::getClassName($controllerClass, 'Controller'),
            'module' => $moduleClass,
            'class'  => $controllerClass,
            'routes' => [], // see bindCommand()
        ];
    }

    /**
     * @param string $controllerClass
     * @param string $method
     * @param string $command
     */
    public static function bindCommand(string $controllerClass, string $method, string $command): void
    {
        self::$commands[$controllerClass]['routes'][] = [
            'method'  => $method,
            'command' => $command ?: $method,
        ];
    }
}
