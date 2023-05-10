<?php

namespace Telegram\Bot\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Telegram\Bot\FakeBot;
use Telegram\Bot\BotManager;

/**
 * @see \Telegram\Bot\BotManager
 *
 * @method static array getBots()
 * @method static null|string getDefaultBotName()
 * @method static \Telegram\Bot\BotManager setDefaultBotName(string $name)
 * @method static \Telegram\Bot\Bot bot(?string $name = null)
 * @method static \Telegram\Bot\Bot reconnect(?string $name = null)
 * @method static \Telegram\Bot\BotManager disconnect(?string $name = null)
 * @method static array getBotConfig(?string $name = null)
 * @method static bool hasContainer()
 * @method static \Illuminate\Contracts\Container\Container getContainer()
 * @method static \Telegram\Bot\BotManager setContainer(\Illuminate\Contracts\Container\Container $container)
 * @method static bool hasConfig($key)
 * @method static void config(array|string|?null $key = null, $default = null)
 * @method static array getConfig()
 * @method static \Telegram\Bot\BotManager setConfig(array $config)
 *
 * @see \Telegram\Bot\Api
 *
 * @method static array isLoginAuthDataValid(array $auth_data)
 * @method static void macro($name, $macro)
 * @method static void mixin($mixin, $replace = true)
 * @method static void hasMacro($name)
 * @method static void flushMacros()
 * @method static void macroCall($method, $parameters)
 * @method static \Telegram\Bot\Http\TelegramClient getClient()
 * @method static array getHttpClientConfig()
 * @method static \Telegram\Bot\Api setHttpClientConfig(array $config)
 * @method static string downloadFile(\Telegram\Bot\Objects\File|\Telegram\Bot\Objects\AbstractResponseObject|string $file, string $filename)
 * @method static bool hasToken()
 * @method static string getToken()
 * @method static \Telegram\Bot\Api setToken(string $token)
 * @method static bool banChatMember(array $params)
 * @method static string exportChatInviteLink(array $params)
 * @method static \Telegram\Bot\Objects\ChatInviteLink createChatInviteLink(array $params)
 * @method static \Telegram\Bot\Objects\ChatInviteLink editChatInviteLink(array $params)
 * @method static \Telegram\Bot\Objects\ChatInviteLink revokeChatInviteLink(array $params)
 * @method static bool approveChatJoinRequest(array $params)
 * @method static bool declineChatJoinRequest(array $params)
 * @method static bool setChatPhoto(array $params)
 * @method static bool deleteChatPhoto(array $params)
 * @method static bool setChatTitle(array $params)
 * @method static bool setChatDescription(array $params)
 * @method static bool pinChatMessage(array $params)
 * @method static bool unpinChatMessage(array $params)
 * @method static bool unpinAllChatMessages(array $params)
 * @method static bool leaveChat(array $params)
 * @method static bool unbanChatMember(array $params)
 * @method static bool restrictChatMember(array $params)
 * @method static bool promoteChatMember(array $params)
 * @method static bool setChatAdministratorCustomTitle(array $params)
 * @method static bool banChatSenderChat(array $params)
 * @method static bool unbanChatSenderChat(array $params)
 * @method static bool setChatPermissions(array $params)
 * @method static \Telegram\Bot\Objects\Chat getChat(array $params)
 * @method static array getChatAdministrators(array $params)
 * @method static int getChatMemberCount(array $params)
 * @method static \Telegram\Bot\Objects\ChatMember getChatMember(array $params)
 * @method static bool setChatStickerSet(array $params)
 * @method static bool deleteChatStickerSet(array $params)
 * @method static bool setMyCommands(array $params)
 * @method static bool deleteMyCommands(array $params = [])
 * @method static array getMyCommands(array $params = [])
 * @method static \Telegram\Bot\Objects\Updates\Message|bool editMessageText(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message|bool editMessageCaption(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message|bool editMessageMedia(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message|bool editMessageReplyMarkup(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Poll stopPoll(array $params)
 * @method static bool deleteMessage(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendGame(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message setGameScore(array $params)
 * @method static array getGameHighScores(array $params)
 * @method static \Telegram\Bot\Objects\User getMe()
 * @method static \Telegram\Bot\Objects\UserProfilePhotos getUserProfilePhotos(array $params)
 * @method static \Telegram\Bot\Objects\File getFile(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendLocation(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message|bool editMessageLiveLocation(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message|bool stopMessageLiveLocation(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendMessage(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message forwardMessage(array $params)
 * @method static int copyMessage(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendPhoto(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendAudio(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendDocument(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendVideo(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendAnimation(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendVoice(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendVideoNote(array $params)
 * @method static array sendMediaGroup(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendVenue(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendContact(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendPoll(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendDice(array $params)
 * @method static bool sendChatAction(array $params)
 * @method static bool setPassportDataErrors(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendInvoice(array $params)
 * @method static bool answerShippingQuery(array $params)
 * @method static bool answerPreCheckoutQuery(array $params)
 * @method static bool answerCallbackQuery(array $params)
 * @method static bool answerInlineQuery(array $params)
 * @method static \Telegram\Bot\Objects\Updates\Message sendSticker(array $params)
 * @method static \Telegram\Bot\Objects\StickerSet getStickerSet(array $params)
 * @method static \Telegram\Bot\Objects\File uploadStickerFile(array $params)
 * @method static bool createNewStickerSet(array $params)
 * @method static bool addStickerToSet(array $params)
 * @method static bool setStickerPositionInSet(array $params)
 * @method static bool deleteStickerFromSet(array $params)
 * @method static bool setStickerSetThumb(array $params)
 * @method static array getUpdates(array $params = [])
 * @method static array confirmUpdate(int $highestId)
 * @method static bool setWebhook(array $params)
 * @method static bool deleteWebhook()
 * @method static \Telegram\Bot\Objects\WebhookInfo getWebhookInfo()
 * @method static \Telegram\Bot\Objects\Update getWebhookUpdate()
 * @method static bool removeWebhook()
 * @method static bool logOut()
 * @method static bool close()
 *
 * @see \Telegram\Bot\Commands\CommandBus
 *
 * @method static array getCommands()
 * @method static \Telegram\Bot\Commands\CommandBus addCommands(array|string $commands)
 * @method static \Telegram\Bot\Commands\CommandBus addCommand(string $command, \Telegram\Bot\Commands\CommandInterface|string $commandClass)
 * @method static \Telegram\Bot\Commands\CommandBus removeCommand(string $name)
 * @method static \Telegram\Bot\Commands\CommandBus removeCommands(array $names)
 * @method static string parseCommand(string $text, int $offset, int $length)
 * @method static \Telegram\Bot\Objects\Update handler(\Telegram\Bot\Objects\Update $update)
 * @method static void execute(\Telegram\Bot\Commands\CommandInterface|string $commandName, \Telegram\Bot\Objects\Update $update, \Telegram\Bot\Objects\MessageEntity|array $entity, bool $isTriggered = false)
 * @method static \Telegram\Bot\Commands\CommandInterface resolveCommand($command)
 * @method static bool hasBot()
 * @method static null|\Telegram\Bot\Bot getBot()
 * @method static \Telegram\Bot\Commands\CommandBus setBot(\Telegram\Bot\Bot $bot)
 */
class Telegram extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return BotManager::class;
    }


    public static function fake()
    {
        FakeBot::$recording = true;
    }

}
