<?php declare(strict_types=1);

namespace SwoftTest\WebSocket\Server\Fixture;

use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\WebSocket\Server\Annotation\Mapping\OnClose;
use Swoft\WebSocket\Server\Annotation\Mapping\OnHandShake;
use Swoft\WebSocket\Server\Annotation\Mapping\OnOpen;
use Swoft\WebSocket\Server\Annotation\Mapping\WsModule;
use Swoft\WebSocket\Server\Contract\WsModuleInterface;
use Swoft\WebSocket\Server\MessageParser\JsonParser;
use SwoftTest\WebSocket\Server\Fixture\Chat\ChatController;
use SwoftTest\WebSocket\Server\Fixture\Chat\UserController;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Class ChatModule
 * @since 2.0
 *
 * @WsModule(
 *     path="/chat",
 *     messageParser=JsonParser::class,
 *     controllers={UserController::class, ChatController::class}
 * )
 */
class ChatModule implements WsModuleInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $defaultCommand = 'default';

    /**
     * command handlers map
     * @var array
     * handler is a method name in ws controller, or is a class implement MessageHandlerInterface
     * [
     *   'command name' => 'callback handler'
     * ]
     */
    private $handlers = [];

    public function init(): void
    {
        $this->options = $this->configure();
        // $this->dispatcher = new MessageDispatcher($this->registerCommands());
    }

    /**
     * 在这里你可以验证握手的请求信息
     * - 必须返回含有两个元素的array
     *  - 第一个元素的值来决定是否进行握手
     *  - 第二个元素是response对象
     * - 可以在response设置一些自定义header,body等信息
     *
     * @OnHandShake()
     * @param Request  $request
     * @param Response $response
     * @return array
     * [ self::ACCEPT, $response ]
     */
    public function checkHandshake(Request $request, Response $response): array
    {
        // TODO: Implement checkHandshake() method.
        return [];
    }

    /**
     * @OnOpen()
     * @param Server  $server
     * @param Request $request
     * @param int     $fd
     */
    public function onOpen(Server $server, Request $request, int $fd): void
    {
        // TODO: Implement onOpen() method.
    }

    /**
     * @OnClose()
     * on connection closed
     * - you can do something. eg. record log
     * @param Server $server
     * @param int    $fd
     */
    public function onClose(Server $server, int $fd): void
    {
        // TODO: Implement onClose() method.
    }

    /**
     * @todo
     * @return array
     */
    protected function configure(): array
    {
        return [
            'pingInterval' => 10000, // ms
            'pingTimeout'  => 5000, // ms
        ];
    }

    // protected function registerOperators(): array
    // protected function registerHandlers(): array
    protected function registerCommands(): array
    {
        return [
            // handler is a method name in the controller, or is a class implement CommandInterface
            // command name => handler
            'default' => 'defaultCommand',
            // 'login' => 'LoginHandler',
            // 'message' => 'MessageHandler',
            // 'logout' => 'LogoutHandler',
            // 'createRoom' => 'CreateRoomHandler',
            // 'some command' => 'Some::class',
        ];
    }

    /**
     * @param Frame $frame
     */
    public function defaultCommand(Frame $frame): void
    {
        //\ws()->send("hello, we have received your message: $body", $frame->fd);
        \server()->push($frame->fd, "hello, we have received your message: {$frame->data}");
    }

    /**
     * @param Server $server
     * @param Frame  $frame
     * data structure:
     * [
     *  'cmd' => 'name', // command name
     *  'body' => ...    // body data
     * ]
     */
    public function onMessage(Server $server, Frame $frame): void
    {
    }

    /**
     * @param Frame $frame
     */
    protected function onFormatError(Frame $frame): void
    {
        \server()->push($frame->fd, 'your sent data format is invalid');
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }
}
