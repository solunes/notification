<?php

namespace Solunes\Notification\App\Console;

use Illuminate\Console\Command;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza una notificación a MK.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $text = 'Texto de prueba con atención y cariño.';
        $this->info('Comenzando la prueba de Notificación enviando a MK: '.$text);
        $response = \Notification::sendNotificationToUser(1, $text);
        $this->info('Notificación enviada a MK correctamente. Respuesta: '.$response);
    }
}
