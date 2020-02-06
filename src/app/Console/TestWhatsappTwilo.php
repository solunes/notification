<?php

namespace Solunes\Notification\App\Console;

use Illuminate\Console\Command;

class TestWhatsappTwilo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-whatsapp-twilo';

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
        $this->info('Comenzando la prueba de Whatsapp enviando a MK: '.$text);
        $response = \Notification::sendWhatsappTwilo($number, $text);
        if($response=='queued'||$response=='sent'){
            $this->info('Whatsapp enviado a MK correctamente. Respuesta: '.$response);
        } else {
            $this->info('Whatsapp NO enviado a MK. Respuesta: '.$response);
        }
    }
}
