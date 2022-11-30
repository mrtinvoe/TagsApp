<?php

namespace TagsApp\Workflows\JsTesting;

use System\Entities\Controllers\Scripts;
use TagsApp\Entities\Controllers\Users;
use Tester\Entities\Controllers\Questions;
use WBoX\Classes\Controller\Driver\ControllerAccessoryAccessor;
use WBoX\Classes\Controller\Drivers\ViewController;
use WBoX\Classes\Entity\EntityManagerFactory;
use WBoX\Classes\System\Messaging\ResponseEventsAccessor;
use WBoX\Classes\System\Messaging\SystemMessagingAccessor;
use WBoX\Classes\ViewScript\Alert;
use WBoX\Components\Button;
use WBoX\Components\CheckBox;
use WBoX\Components\Classes\ListViewActionBuilder;
use WBoX\Components\Classes\SelectOptionBuilder;
use WBoX\Components\EditBox;
use WBoX\Components\Label;
use WBoX\Components\LightBlue\FieldSet;
use WBoX\Components\ListTable;
use WBoX\Components\ListView;
use WBoX\Components\Select;
use WBoX\Components\Window;

class TestWorkflow extends ViewController
{
    /**
     * @component(name="wndTestWf",test="test")
     * @var Window
     */
    public Window $wndTestWf;

    /**
     * @component(name="lblTest",test="test")
     * @var Label
     */
    public Label $lblTest;

    /**
     * @component(name="flsListView",test="test")
     * @var FieldSet
     */
    public FieldSet $flsListView;

    /**
     * @component(name="lstListView", test="test")
     * @var ListView
     */
    public ListView $lstListView;

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
     * @component(name="closeBtn",test="test")
     * @var Button
     */
    public Button $closeBtn;

    /**
     * @component(name="saveBtn",test="test")
     * @var Button
     */
    public Button $saveBtn;

    public function __construct(
        private ControllerAccessoryAccessor $controllerAccessoryAccessor,
        private EntityManagerFactory $entityManagerFactory,
        private SystemMessagingAccessor $systemMessagingAccessor,
        private ResponseEventsAccessor $responseEventsAccessor
    ){}

    public function init()
    {

    }

    public function indexAction()
    {
        $this->closeBtn->onButtonPressedEvent(fn() => $this->closeWindow());
        $this->saveBtn->onButtonPressedEvent(fn() => (new Alert())->setText('reakce va dalším wnd')->setType('success')->create());

        $this->lblTest->setText("##test");

        $insert = [];
        foreach ((new Questions($this->entityManagerFactory, )) as $question)
        {
            $insert[] = ['id' => $question->getId(), 'zneni' => $question->getValue()];
        }
        $this->lstListView->setDataUpdate(['insert' => $insert]);
    }


    private function closeWindow(): void
    {
        $this->wndTestWf->destroyComponent();
        $this->controllerAccessoryAccessor->get()->closeWorkflow();
    }

}
