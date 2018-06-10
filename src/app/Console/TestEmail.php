<?php

namespace Solunes\Notification\App\Console;

use Illuminate\Console\Command;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza un email a MK.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $email_title = 'Solunes Digital | Email de Prueba';
        $message_title = 'Email de Prueba';
        $message_content = 'Texto de prueba con atención y cariño.';
        $to_array = ['edumejia30@gmail.com','edu_mejia30@hotmail.com'];
        $this->info('Comenzando la prueba de Email enviando a MK: '.$message_title);
        $response = \Notification::sendEmail($email_title, $to_array, $message_title, $message_content);
        $this->info('Email enviado a MK correctamente. Respuesta: '.$response);
    }
}
