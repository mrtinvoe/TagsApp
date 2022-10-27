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



    private int $val;

    private TagsWorker $tagsWorker;

    private array $tagsArr = [
        460, 461, 462, 463, 464, 465, 466, 467, 468
    ];




    public function __construct(
        private ControllerAccessoryAccessor $controllerAccessoryAccessor,
        private EntityManagerFactory $entityManagerFactory,
        private SharedMemoryAccessor $sharedMemoryAccessor,
        private SystemMessagingAccessor $systemMessagingAccessor,
        private ResponseEventsAccessor $responseEventsAccessor,
        //private TagsWorker $tagsWorker,
    ){}

    public function init()
    {

    }

    public function indexAction()
    {
        $tagsWorker = new TagsWorker($this->entityManagerFactory, $this->controllerAccessoryAccessor, $this->sharedMemoryAccessor, $this->systemMessagingAccessor, $this->responseEventsAccessor);
        $tagsWorker->buildPlayground("⭕");
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
            $btn->setName("btn1".$i);
            $btn->setHeight(100);
            $btn->setWidth(100);
            $this->controllerAccessoryAccessor->get()->addControllerComponent($btn);

            $btn->onButtonPressedEvent(function() use ($i){
                $tagWriter = new TagWriter($this->sharedMemoryAccessor);
                $tagWriter->writeTag($this->tagsArr[$i-1], "⭕");
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


    private function closeWindow(): void
    {
        $this->wndPlayground->destroyComponent();
        $this->controllerAccessoryAccessor->get()->closeWorkflow();
    }
}
/*$this->btnChangeTag->onButtonPressedEvent(function(){
           $tagWriter = new TagWriter($this->sharedMemoryAccessor);

           if(empty($this->val)){
               $this->val = 1;
           }
           else{
               if($this->val == 1) $this->val = 2;
               else $this->val = 0;
           }


           $tagWriter->writeTag(460, $this->val);
           errlog("Zapsána".$this->val." do tagu 460");
       });*/
