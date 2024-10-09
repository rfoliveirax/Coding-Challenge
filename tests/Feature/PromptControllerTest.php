<?php

use App\Models\Prompt;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;
use function Pest\Laravel\{actingAs, get, post, put};

beforeEach(function () {
    // Mock user authentication
    $this->user = \App\Models\User::factory()->create();
    actingAs($this->user);
});

it('displays a listing of the resource', function () {
    $prompts = Prompt::factory()->count(3)->create(['user_id' => $this->user->id]);

    get(route('prompt'))
        ->assertInertia(fn ($page) => $page
            ->component('Prompt/MyPrompts')
            ->has('conversations', 3)
        );
});

it('starts prompting', function () {
    $input           = 'Hello, how are you?';
    $responseMessage = 'I am fine, thank you!';
    OpenAI::fake([
        \OpenAI\Responses\Chat\CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'content' => $responseMessage,
                        'role'    => 'assistant',
                    ],
                ],
            ],
        ]),
    ]);

    post(route('prompt.start'), ['prompt_input' => $input])
        ->assertRedirect(route('prompt.continue', Prompt::first()->prompt_chain_id));

    $this->assertDatabaseHas('prompts', [
        'message' => $input,
        'role'    => 'user',
        'user_id' => $this->user->id,
    ]);

    $this->assertDatabaseHas('prompts', [
        'message' => $responseMessage,
        'role'    => 'assistant',
        'user_id' => $this->user->id,
    ]);
});

it('continues chatting', function () {
    $promptChainId = Str::uuid()->toString();
    Prompt::factory()->count(3)->create(['user_id' => $this->user->id, 'prompt_chain_id' => $promptChainId]);

    get(route('prompt.continue', $promptChainId))
        ->assertInertia(fn ($page) => $page
            ->component('Prompt/ContinueConversation')
            ->has('messages', 3)
            ->where('promptChainId', $promptChainId)
        );
});

it('adds a new message to the conversation', function () {
    $promptChainId   = Str::uuid()->toString();
    $input           = 'What is the weather like?';
    $responseMessage = 'The weather is sunny.';

    Prompt::factory()->count(3)->create(['user_id' => $this->user->id, 'prompt_chain_id' => $promptChainId]);
    OpenAI::fake([
        \OpenAI\Responses\Chat\CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'content' => $responseMessage,
                        'role'    => 'assistant',
                    ],
                ],
            ],
        ]),
    ]);

    put(route('prompt.new-message', $promptChainId), ['prompt_input' => $input])
        ->assertRedirect(route('prompt.continue', $promptChainId));

    $this->assertDatabaseHas('prompts', [
        'message'         => $input,
        'role'            => 'user',
        'user_id'         => $this->user->id,
        'prompt_chain_id' => $promptChainId,
    ]);

    $this->assertDatabaseHas('prompts', [
        'message'         => $responseMessage,
        'role'            => 'assistant',
        'user_id'         => $this->user->id,
        'prompt_chain_id' => $promptChainId,
    ]);
});
it('has a root page', function () {
    $response = $this->get('/prompt');

    $response->assertStatus(200);
});
