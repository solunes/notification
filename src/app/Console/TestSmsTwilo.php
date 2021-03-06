<?php

namespace Solunes\Notification\App\Console;

use Illuminate\Console\Command;

class TestSmsTwilo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-sms-twilo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza un SMS a MK.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $text = 'Texto de prueba con atención y cariño.';
        $number = '70554450';
        $this->info('Comenzando la prueba de SMS enviando a MK: '.$text);
        $response = \Notification::sendSmsTwilo($number, $text);
        if($response=='queued'||$response=='sent'){
            $this->info('SMS enviado a MK correctamente. Respuesta: '.$response);
        } else {
            $this->info('SMS NO enviado a MK. Respuesta: '.$response);
        }
    }
}
