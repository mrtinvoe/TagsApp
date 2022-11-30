<?php

namespace TagsApp\Workflows\JsTesting;

use System\Entities\Controllers\Scripts;
use TagsApp\Entities\Controllers\Users;
use Tester\Entities\Controllers\Questions;
use Tester\Entities\Controllers\Tests;
use WBoX\Classes\Controller\Driver\ControllerAccessoryAccessor;
use WBoX\Classes\Controller\Drivers\ViewController;
use WBoX\Classes\Entity\EntityManagerFactory;
use WBoX\Classes\System\Messaging\ResponseEventsAccessor;
use WBoX\Classes\System\Messaging\SystemMessagingAccessor;
use WBoX\Classes\ViewScript\Alert;
use WBoX\Components\Button;
use WBoX\Components\CheckBox;
use WBoX\Components\Classes\ComboBoxOptionBuilder;
use WBoX\Components\Classes\ListViewActionBuilder;
use WBoX\Components\Classes\SelectOptionBuilder;
use WBoX\Components\EditBox;
use WBoX\Components\LightBlue\FieldSet;
use WBoX\Components\ListTable;
use WBoX\Components\ListView;
use WBoX\Components\Select;
use WBoX\Components\Window;
use WBoX\Components\ComboBox;

class JsTesting extends ViewController
{
    /**
     * @component(name="wndTest",test="test")
     * @var Window
     */
    public Window $wndTest;

    /**
     * @component(name="flsCmb",test="test")
     * @var FieldSet
     */
    public FieldSet $flsCmb;

    /**
     * @component(name="flsSelect",test="test")
     * @var FieldSet
     */
    public FieldSet $flsSelect;

    /**
     * @component(name="flsShortcuts",test="test")
     * @var FieldSet
     */
    public FieldSet $flsShortcuts;

    /**
     * @component(name="flsListView",test="test")
     * @var FieldSet
     */
    public FieldSet $flsListView;

    /**
     * @component(name="lstListView",test="test")
     * @var ListView
     */
    public ListView $lstListView;

    /**
     * @component(name="txtName",test="test")
     * @var EditBox
     */
    public EditBox $txtName;

    /**
     * @component(name="switcheryCheckBox",test="test")
     * @var CheckBox
     */
    public CheckBox $switcheryCheckBox;

    /**
     * @component(name="checkBox",test="test")
     * @var CheckBox
     */
    public CheckBox $checkBox;

    /**
     * @component(name="circleCheckBox",test="test")
     * @var CheckBox
     */
    public CheckBox $circleCheckBox;

    /**
     * @component(name="defaultCheckBox",test="test")
     * @var CheckBox
     */
    public CheckBox $defaultCheckBox;

    /**
     * @component(name="selectUser",test="test")
     * @var Select
     */
    public Select $selectUser;

    /**
     * @component(name="flsListTable",test="test")
     * @var FieldSet
     */
    public FieldSet $flsListTable;

    /**
     * @component(name="lstListTable", test="test")
     * @var ListTable
     */
    public ListTable $lstListTable;

    /**
     * @component(name="btnTest",test="test")
     * @var Button
     */
    public Button $btnTest;

    /**
     * @component(name="saveBtn",test="test")
     * @var Button
     */
    public Button $saveBtn;

    /**
     * @component(name="cmbUser",test="test")
     * @var ComboBox
     */
    public ComboBox $cmbUser;

    private array $values = ["neco", "nevim"];

    public function __construct(
        private ControllerAccessoryAccessor $controllerAccessoryAccessor,
        private EntityManagerFactory $entityManagerFactory,
        private SystemMessagingAccessor $systemMessagingAccessor,
        private ResponseEventsAccessor $responseEventsAccessor
    ){}

    public function init()
    {
        $this->lstListView->onActionCalledEvent(function(string $actionName, ) {

            if ($actionName == 'edit')
                errlog("ahoj");

        });

        ($selectOptionBuilder = new SelectOptionBuilder())->setGroupName('Users');
        foreach ((new Users($this->entityManagerFactory)) as $user) {
            $selectOptionBuilder->addOption($user->getId(), $user->getName());
        }
        $this->selectUser->getOptionsSetter()->add($selectOptionBuilder)->create();

        $this->initComboBoxes();

        $this->txtName->setWhisperer($this->values);

        $this->lstListView->onActionCalledEvent( function(){

            errlog("ahoj");

        });

    }

    public function indexAction()
    {


        $this->btnTest->onButtonPressedEvent(fn() => $this->fillWhisperer());
        $this->saveBtn->onButtonPressedEvent(fn() => $this->openNewWorkflow());

        $insert = [];
        foreach ((new Questions($this->entityManagerFactory, )) as $question)
        {
            $insert[] = ['id' => $question->getId(), 'zneni' => $question->getValue(), 'typ' => $question->getAnswerType()];
        }
        $this->lstListView->setDataUpdate(['insert' => $insert]);
    }

    private function initComboBoxes(): void{
        $cmbUser = $this->cmbUser->getOptionsSetter();
        foreach ((new \Tester\Entities\Controllers\Users($this->entityManagerFactory)) as $item)
            $cmbUser->add((new ComboBoxOptionBuilder($item->getId()))->setText($item->getName()));
        $cmbUser->create();
    }

    private function fillWhisperer(){
        $array = [];
        for($i = 0; $i<5; $i++){
            $array[$i] = $this->generateRandomString();
            errlog($array[$i]);
        }

        $this->txtName->setWhisperer($array);

    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    private function closeWindow(): void
    {
        $this->wndTest->destroyComponent();
        $this->controllerAccessoryAccessor->get()->closeWorkflow();
    }

    private function openNewWorkflow(): void{
        (new Alert())->setText('vytvořeno nové okno')->setType('success')->create();
        $this->controllerAccessoryAccessor->get()->createWorkflow('TagsApp/JsTesting/TestWorkflow', []);
    }
}
