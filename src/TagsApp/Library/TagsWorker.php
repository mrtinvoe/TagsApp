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

class TagsWorker{

    private array $tagsArr = [
        460, 461, 462, 463, 464, 465, 466, 467, 468
    ];

    public function __construct(
            private EntityManagerFactory $entityManagerFactory,
            private ControllerAccessoryAccessor $controllerAccessoryAccessor,
            private SharedMemoryAccessor $sharedMemoryAccessor,
            private SystemMessagingAccessor $systemMessagingAccessor,
            private ResponseEventsAccessor $responseEventsAccessor,
        ){}

        public function listen(int $tagId): void{
            $this->controllerAccessoryAccessor->get()->registerTableUpdate((new TagsLive($this->entityManagerFactory, ['tag' => $tagId])), function (TagLive $tagLive) {
                errlog($tagLive->getValue());
            });
        }

        public function makeTurn(int $tagId, string $character, \WBoX\Components\Button $button): void{

            $tagWriter = new TagWriter($this->sharedMemoryAccessor);

            $tagWriter->writeTag($tagId, $character);
            $this->controllerAccessoryAccessor->get()->registerTableUpdate((new TagsLive($this->entityManagerFactory, ['tag' => $tagId])), function (TagLive $tagLive) use ($button) {
                if(!empty($tagLive)){
                    $button->setDisabled();
                    $button->setLabel($tagLive->getValue());
                }
            });
        }

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
                $btn->onButtonPressedEvent(function() use ($i, $character){
                    $tagWriter = new TagWriter($this->sharedMemoryAccessor);
                    $tagWriter->writeTag($this->tagsArr[$i-1], $character);
                });

                $btn->setLabel(" ");

                $this->controllerAccessoryAccessor->get()->registerTableUpdate((new TagsLive($this->entityManagerFactory, ['tag' => $this->tagsArr[$i-1]])), function (TagLive $tagLive) use ($btn) {
                    if(!empty($tagLive->getValue())){
                        if($tagLive->getValue() == "❌" || $tagLive->getValue() == "⭕"){
                            errlog("ma to hodnotu".$tagLive->getValue());
                            $btn->setLabel($tagLive->getValue());
                            $btn->setDisabled();
                        }
                    }

                });
            }
    }

        public function getGameTags(): array{
            return $this->tagsArr;
        }
    }