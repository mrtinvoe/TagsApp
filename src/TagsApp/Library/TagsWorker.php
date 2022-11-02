<?php
namespace TagsApp\Library;

use RaamTags\Entities\Controllers\TagsLive;
use RaamTags\Entities\Execute\TagLive;
use \WBoX\Classes\Controller\Driver\ControllerAccessoryAccessor;
use WBoX\Classes\Entity\EntityManagerFactory;
use \Evromat\Workflows\Tools\Assets\TagWriter;
use \WBoX\Classes\SharedMemoryAccessor;
use WBoX\Classes\System\Messaging\ResponseEventsAccessor;
use WBoX\Classes\System\Messaging\SystemMessagingAccessor;
use WBoX\Components\Xamarin\Button;

class TagsWorker
{

    private array $tagsArr = [
        460, 461, 462, 463, 464, 465, 466, 467, 468
    ];

    private int $state = 0;

    public function __construct(
            private EntityManagerFactory $entityManagerFactory,
            private ControllerAccessoryAccessor $controllerAccessoryAccessor,
            private SharedMemoryAccessor $sharedMemoryAccessor,
            private SystemMessagingAccessor $systemMessagingAccessor,
            private ResponseEventsAccessor $responseEventsAccessor,
        ){}

        public function buildPlayground(string $character): void{

            for($i = 1; $i<=9; $i++){
                if($character == "❌"){
                    $btn = new Button($this->systemMessagingAccessor, $this->responseEventsAccessor,$this->controllerAccessoryAccessor);
                }
                else{
                    $btn = new \WBoX\Components\LightBlue\Button($this->systemMessagingAccessor, $this->responseEventsAccessor,$this->controllerAccessoryAccessor);
                }

                if($i <= 3){
                    $btn->setParent('flsRow1');
                }
                else{
                    if($i <=6){
                        $btn->setParent('flsRow2');
                    }
                    else{
                        $btn->setParent('flsRow3');
                    }
                }
                $btn->setName("btn".$i);
                $btn->setHeight(100);
                $btn->setWidth(100);




                $this->controllerAccessoryAccessor->get()->addControllerComponent($btn);

                $btn->onButtonPressedEvent(
                    function() use($i, $character, $btn){
                        if($character == "⭕"){
                            if($btn instanceof \WBoX\Components\LightBlue\Button) $btn->setDisabled();
                        }
                        else{
                            if($btn instanceof Button) $btn->setDisabled();
                        }
                    }
                );
                $btn->setLabel(" ");

                $this->makeRecord($this->tagsArr[$i-1], $btn, $character);

            }

            /*$this->controllerAccessoryAccessor->get()->registerTableUpdate((new TagsLive($this->entityManagerFactory, ['tag' => $this->tagsArr[$i-1]])),
                function (TagLive $tagLive) use ($character, $btn) {
                    if($character == "❌" && $tagLive->getValue() == "❌"){
                        $btn->setDisabled();
                    }
                    if($character == "⭕" && $tagLive->getValue() == "⭕"){
                        $btn->setDisabled();
                    }

                    errlog($tagLive->getValue() ?? "nic tu neny");
                });*/
        }

        public function makeTurn(int $tagId, $character): void{
            $tagWriter = new TagWriter($this->sharedMemoryAccessor);
            $tagWriter->writeTag($tagId, $character);
        }

        public function makeRecord(int $tagId, $button, $character): void{
            $this->controllerAccessoryAccessor->get()->registerTableUpdate((new TagsLive($this->entityManagerFactory, ['tag' => $tagId])),
                function (TagLive $tagLive) use ($tagId, $button, $character) {
                if(!empty($tagLive->getValue())){
                    if($tagLive->getValue() == "❌" || $tagLive->getValue() == "⭕"){

                        $button->setLabel($tagLive->getValue());
                        $button->setDisabled();
                    }
                }
            });
        }

        public function listenState(): void{

        }


        public function setState(int $state): void{
            $this->state = $state;
        }

        public function getState(): int{
            return $this->state;
        }
    }