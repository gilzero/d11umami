<?php

declare(strict_types=1);

namespace Drupal\Tests\ai\Kernel\OperationType\Chat;

use Drupal\KernelTests\KernelTestBase;
use Drupal\ai\Exception\AiRequestErrorException;
use Drupal\ai\OperationType\Chat\ChatInput;
use Drupal\ai\OperationType\Chat\ChatMessage;
use Drupal\ai\OperationType\Chat\ChatOutput;
use Drupal\ai\OperationType\Chat\StreamedChatMessageIteratorInterface;

/**
 * This tests the Chat calling.
 *
 * @coversDefaultClass \Drupal\ai\OperationType\Chat\ChatInterface
 *
 * @group ai
 */
class ChatInterfaceTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'ai',
    'provider_openai',
    'key',
    'file',
    'user',
    'field',
    'system',
  ];

  /**
   * Setup the test.
   */
  protected function setUp(): void {
    parent::setUp();

    // Create an OpenAI mockup key.
    /** @var \Drupal\key\Entity\Key */
    $key = \Drupal::entityTypeManager()
      ->getStorage('key')
      ->create([
        'id' => 'mockup_openai',
        'label' => 'Mockup OpenAI',
        'key_provider' => 'config',
      ]);
    $key->setKeyValue('abc123');
    $key->save();

    // DDEV or local.
    $host = getenv('DDEV_PROJECT') ? 'http://mockoon:3010/v1' : 'http://localhost:3010/v1';

    // Setup OpenAI as the provider.
    \Drupal::configFactory()
      ->getEditable('provider_openai.settings')
      ->set('host', $host)
      ->set('api_key', 'mockup_openai')
      ->save();

    // Install entity schemas.
    $this->installEntitySchema('user');
    $this->installEntitySchema('file');
    $this->installSchema('file', [
      'file_usage',
    ]);
  }

  /**
   * Test the chat service with mockup OpenAI Provider.
   */
  public function testChatNormalized(): void {
    $text = 'Can you help me with something?';
    $provider = \Drupal::service('ai.provider')->createInstance('openai');
    $input = new ChatInput([
      new ChatMessage('user', $text),
    ]);
    $chat_response = $provider->chat($input, 'gpt-4o');
    // Should be a ChatOutput object.
    $this->assertInstanceOf(ChatOutput::class, $chat_response);
    // Should have a message.
    $message = $chat_response->getNormalized();
    $this->assertInstanceOf(ChatMessage::class, $message);

    // Response should be a string and be the following.
    $response_text = 'Great! I can help with that!';
    $this->assertIsString($message->getText());
    $this->assertEquals($response_text, $message->getText());
  }

  /**
   * Test that the streaming chat works.
   */
  public function testChatStream(): void {
    $text = 'Can you help me with something?';
    $provider = \Drupal::service('ai.provider')->createInstance('openai');
    $input = new ChatInput([
      new ChatMessage('user', $text),
    ]);
    // Set to streaming.
    $provider->streamedOutput(TRUE);
    $chat_response = $provider->chat($input, 'gpt-4o');
    // Should be a ChatOutput object.
    $this->assertInstanceOf(ChatOutput::class, $chat_response);
    // Should have a streaming response.
    $message = $chat_response->getNormalized();
    $this->assertInstanceOf(StreamedChatMessageIteratorInterface::class, $message);

    // Response should be a string and be the following.
    $response_text = 'Great! I can help with that!';
    // Its an iterator.
    foreach ($message as $message_part) {
      $this->assertIsString($message_part->getText());
      $this->assertEquals($response_text, $message_part->getText());
    }

  }

  /**
   * Test some errors.
   */
  public function testErrors(): void {
    $provider = \Drupal::service('ai.provider')->createInstance('openai');
    // Empty input.
    $input = new ChatInput([
      new ChatMessage('', ''),
    ]);
    // This should throw an error because lacking input.
    $this->expectException(AiRequestErrorException::class);
    $provider->chat($input, 'gpt-4o');

    // Working input.
    $input = new ChatInput([
      new ChatMessage('user', 'hello there'),
    ]);
    // This should throw an error because lacking model.
    $this->expectException(AiRequestErrorException::class);
    $provider->chat($input);
  }

  /**
   * Test that dynamic authentication works and exception is thrown on failure.
   */
  public function testDynamicAuthenticationAndAuthenticationException(): void {
    $text = 'A cow';
    $provider = \Drupal::service('ai.provider')->createInstance('openai');
    $input = new ChatInput([
      new ChatMessage('user', $text),
    ]);
    $provider->setAuthentication('faulty_key');
    $this->expectException(AiRequestErrorException::class);
    $provider->chat($input, 'gpt-4o');

    // Set back to the correct key.
    $provider->setAuthentication('abc123');
    $chat_response = $provider->chat($input, 'dall-e-3');
    $this->assertInstanceOf(ChatOutput::class, $chat_response);
  }

}