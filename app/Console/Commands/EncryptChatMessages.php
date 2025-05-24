<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use Illuminate\Support\Facades\DB;

class EncryptChatMessages extends Command
{
    protected $signature = 'chat:encrypt-messages';
    protected $description = 'Encrypt existing chat messages';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting to encrypt chat messages...');
        
        // Get all chats with unencrypted messages
        $chats = DB::table('chats')
            ->whereNotNull('message')
            ->whereNull('message_encrypted')
            ->get();
            
        $count = count($chats);
        $this->info("Found {$count} messages to encrypt");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        foreach ($chats as $chatData) {
            // Get the model instance
            $chat = Chat::find($chatData->id);
            
            if ($chat) {
                // Store the original message
                $originalMessage = $chatData->message;
                
                // Set the message which will trigger encryption
                $chat->message = $originalMessage;
                
                // Save the model
                $chat->save();
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\nEncryption completed!");
    }
}