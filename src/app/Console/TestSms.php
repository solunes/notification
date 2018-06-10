<?php

namespace Solunes\Notification\App\Console;

use Illuminate\Console\Command;

class TestSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-sms';

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
        $response = \Notification::sendSms($number, $text, 'Solunes');
        $this->info('SMS enviado a MK correctamente. Respuesta: '.$response);
    }
}
