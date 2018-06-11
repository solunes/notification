<?php

namespace Solunes\Notification\App\Console;

use Illuminate\Console\Command;

class TestAudio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-audio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza un Audio a MK.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $text = 'Texto de prueba con atención y cariño.';
        $this->info('Comenzando la prueba de Audio: '.$text);
        $response = \Notification::generateAudio($text, 'female');
        $this->info('Audio generado orrectamente Revisar en: '.$response);
    }
}
