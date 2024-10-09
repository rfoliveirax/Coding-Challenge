<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use OpenAI\Laravel\Facades\OpenAI;

class PromptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Prompt/MyPrompts', [
            'conversations' => Prompt::select('prompt_chain_id', 'message', 'created_at')
                                     ->where('user_id', 1)
                                     ->groupBy('prompt_chain_id')
                                     ->havingRaw('MIN(id)')
                                     ->orderBy('created_at', 'desc')
                                     ->get(),
        ]);
    }

    /**
     * Start prompting
     */
    public function start(Request $request)
    {
        $request->validate([
            'prompt_input' => ['required'],
        ]);

        $result = OpenAI::chat()->create([
            'model'    => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $request->prompt_input],
            ],
        ]);

        $prompt = Prompt::create(
            [
                'message'         => $request->prompt_input,
                'role'            => 'user',
                'user_id'         => $request->user()->id,
                'prompt_chain_id' => Str::uuid(),
            ]
        );

        Prompt::create(
            [
                'message'         => $result->choices[0]->message->content,
                'role'            => $result->choices[0]->message->role,
                'user_id'         => $request->user()->id,
                'prompt_chain_id' => $prompt->prompt_chain_id,
            ]
        );

        return Redirect::route('prompt.continue', $prompt->prompt_chain_id);
    }

    /**
     * Continue chatting
     */
    public function continue(string $promptChainId)
    {
        return Inertia::render('Prompt/ContinueConversation', [
            'messages' => Prompt::where('prompt_chain_id', $promptChainId)->where('user_id', Auth::id())->orderBy('created_at')->get(),
            'promptChainId' => $promptChainId,
        ]);
    }


    /**
     * Continue chatting
     */
    public function newMessage(Request $request, string $promptChainId)
    {
        $messages = Prompt::where('prompt_chain_id', $promptChainId)->where('user_id', Auth::id())->orderBy('created_at')->get();

        $previous_messages = $messages->map(function ($conversation) {
            return ['role' => $conversation->role, 'content' => $conversation->message];
        })->toArray();

        $request->validate([
            'prompt_input' => ['required'],
        ]);

        $result = OpenAI::chat()->create([
            'model'    => 'gpt-3.5-turbo',
            'messages' => array_merge($previous_messages, [['role' => 'user', 'content' => $request->prompt_input]]),
        ]);

        Prompt::create(
            [
                'message'         => $request->prompt_input,
                'role'            => 'user',
                'user_id'         => $request->user()->id,
                'prompt_chain_id' => $promptChainId,
            ]
        );

        Prompt::create(
            [
                'message'         => $result->choices[0]->message->content,
                'role'            => $result->choices[0]->message->role,
                'user_id'         => $request->user()->id,
                'prompt_chain_id' => $promptChainId,
            ]
        );

        return Redirect::route('prompt.continue', $promptChainId);
    }

}
