<?php

namespace TagsApp\Workflows\HomeXam;

use Evromat\Workflows\Tools\Assets\TagWriter;
use RaamTags\Entities\Controllers\TagsLive;
use RaamTags\Entities\Execute\TagLive;
use TagsApp\Library\TagsWorker;
use Tester\Entities\Controllers\Users;
use WBoX\Classes\Controller\Driver\ControllerAccessoryAccessor;
use WBoX\Classes\Controller\Drivers\ViewController;
use WBoX\Classes\Entity\EntityManagerFactory;
use WBoX\Classes\SharedMemoryAccessor;
use WBoX\Classes\System\Messaging\ResponseEventsAccessor;
use WBoX\Classes\System\Messaging\SystemMessagingAccessor;
use WBoX\Components\Xamarin\Button;
use WBoX\Components\Classes\ListViewActionBuilder;
use WBoX\Components\Xamarin\FieldSet;
use WBoX\Components\ListTable;
use WBoX\Components\Xamarin\Window;
use WBoX\Components\Xamarin\Label;

class PlayGround extends ViewController
{
    /**
     * @component(name="wndDashboard",test="test")
     * @var Window
     */
    public Window $wndDashboard;

    /**
     * @component(name="flsRow1",test="test")
     * @var FieldSet
     */
    public FieldSet $flsRow1;

    /**
     * @component(name="flsRow2",test="test")
     * @var FieldSet
     */
    public FieldSet $flsRow2;

    /**
     * @component(name="flsRow3",test="test")
     * @var FieldSet
     */
    public FieldSet $flsRow3;

    private array $tagsArr = [
        460, 461, 462, 463, 464, 465, 466, 467, 468
    ];




    public function __construct(
        private ControllerAccessoryAccessor $controllerAccessoryAccessor,
        private EntityManagerFactory $entityManagerFactory,
        private SharedMemoryAccessor $sharedMemoryAccessor,
        private SystemMessagingAccessor $systemMessagingAccessor,
        private ResponseEventsAccessor $responseEventsAccessor
    ){}

    public function init()
    {
        /*$tagWriter = new TagWriter($this->sharedMemoryAccessor);

        $tagWriter->writeTag(1, 7);*/

    }

    public function indexAction()
    {
        $tagsWorker = new TagsWorker($this->entityManagerFactory, $this->controllerAccessoryAccessor, $this->sharedMemoryAccessor, $this->systemMessagingAccessor, $this->responseEventsAccessor);
        $tagsWorker->buildPlayground("❌");
    }

    private function buildPlayground(){
        for($i = 1; $i<=9; $i++){
            $btn = new Button($this->systemMessagingAccessor, $this->responseEventsAccessor,$this->controllerAccessoryAccessor);
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
            $btn->setName("btn1".$i);
            $btn->setHeight(100);
            $btn->setWidth(100);
            $this->controllerAccessoryAccessor->get()->addControllerComponent($btn);
            $btn->onButtonPressedEvent(function() use ($i){
                $tagWriter = new TagWriter($this->sharedMemoryAccessor);
                $tagWriter->writeTag($this->tagsArr[$i-1], "❌");
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

    /*private function changeColor(): void{
        $this->controllerAccessoryAccessor->get()->registerTableUpdate((new TagsLive($this->entityManagerFactory, ['tag' => 460])), function (TagLive $tagLive) {
            if($tagLive->getValue() == 1){
                $this->btnTest->setColor('success');
            }
            else{
                $this->btnTest->setColor('danger');
            }


        });
    }*/



    private function closeWindow(): void
    {
        $this->wndDashboard->destroyComponent();
        $this->controllerAccessoryAccessor->get()->closeWorkflow();
    }
}
