<?php

namespace TagsApp\Workflows\HomeWeb;

use Evromat\Workflows\Tools\Assets\TagWriter;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing\Shadow;
use RaamTags\Entities\Controllers\Tags;
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
use WBoX\Components\Button;
use WBoX\Components\Classes\ListViewActionBuilder;
use WBoX\Components\FieldSet;
use WBoX\Components\Label;
use WBoX\Components\ListTable;
use WBoX\Components\Window;

class PlayGround extends ViewController
{
    /**
     * @component(name="wndPlayground",test="test")
     * @var Window
     */
    public Window $wndPlayground;

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

    /**
     * @component(name="closeBtn",test="test")
     * @var Button
     */
    public Button $closeBtn;



    private int $val = 0;

    private TagsWorker $tagsWorker;

    private array $tagsArr = [
        460, 461, 462, 463, 464, 465, 466, 467, 468
    ];

    private array $buttons;




    public function __construct(
        private ControllerAccessoryAccessor $controllerAccessoryAccessor,
        private EntityManagerFactory $entityManagerFactory,
        private SharedMemoryAccessor $sharedMemoryAccessor,
        private SystemMessagingAccessor $systemMessagingAccessor,
        private ResponseEventsAccessor $responseEventsAccessor,
    ){}

    public function init()
    {
        $this->tagsWorker = new TagsWorker($this->entityManagerFactory, $this->controllerAccessoryAccessor, $this->sharedMemoryAccessor, $this->systemMessagingAccessor, $this->responseEventsAccessor);
    }

    public function indexAction()
    {
        $this->buildPlayGround();
    }

    private function buildPlayGround(){

        for($i = 1; $i<=9; $i++){
            $btn = new \WBoX\Components\LightBlue\Button($this->systemMessagingAccessor, $this->responseEventsAccessor,$this->controllerAccessoryAccessor);

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
            $this->buttons[] = $btn;

            $btn->onButtonPressedEvent(
                function() use($btn, $i){
                    $this->tagsWorker->makeTurn($this->tagsArr[$i-1], "⭕");
                }
            );
            $btn->setLabel(" ");

            $this->controllerAccessoryAccessor->get()->registerTableUpdate((new TagsLive($this->entityManagerFactory, ['tag' => $this->tagsArr[$i-1]])),
                function (TagLive $tagLive) use ($btn) {
                    if(!empty($tagLive->getValue())){
                        if($tagLive->getValue() == "⭕" || $tagLive->getValue() == "❌"){
                            $btn->setLabel($tagLive->getValue());
                            $btn->setDisabled();

                            if($tagLive->getValue() == "⭕") $this->tagsWorker->setState(2);
                            if($tagLive->getValue() == "❌") $this->tagsWorker->setState(1);
                            errlog("state: ".$this->tagsWorker->getState());


                        }
                    }
                });
        }

        $i = 0;
        foreach($this->buttons as $button){
            if($button instanceof \WBoX\Components\LightBlue\Button){
                $i++;

                $this->controllerAccessoryAccessor->get()->registerTableUpdate((new TagsLive($this->entityManagerFactory, ['tag' => $this->tagsArr[$i-1]])),
                    function (TagLive $tagLive) use ($button) {
                        if(!empty($tagLive->getValue())){
                            if($this->tagsWorker->getState() == 2){
                                foreach ($this->buttons as $btn){
                                    $btn->setDisabled();
                                }
                            }
                            if($this->tagsWorker->getState() == 1){
                                foreach ($this->buttons as $btn){
                                    if($btn->label == " ")
                                    $btn->setDisabled(false);
                                }
                            }
                        }
                    });
            }


        }

    }

    private function closeWindow(): void
    {
        $this->wndPlayground->destroyComponent();
        $this->controllerAccessoryAccessor->get()->closeWorkflow();
    }
}
