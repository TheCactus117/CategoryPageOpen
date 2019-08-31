<?php

namespace TheCactus117\CategoryPageOpen\Block\Adminhtml;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\App\Area;

/**
 * Block Class OpenCategoryPageButton.
 * @package TheCactus117\CategoryPageOpen\Block\Adminhtml
 */
class OpenCategoryPageButton extends Container
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Emulation
     */
    protected $emulation;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * OpenCategoryPageButton constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Emulation $emulation
     * @param CategoryRepository $categoryRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Emulation $emulation,
        CategoryRepository $categoryRepository,
        array $data = [])
    {
        $this->registry = $registry;
        $this->emulation = $emulation;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve currently edited category object.
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * Sub constructor.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addOpenCategoryPageButton();
    }

    /**
     * Add open category page button to Category page edition.
     */
    protected function addOpenCategoryPageButton()
    {
        $category = $this->getCategory();
        if ($category &&
            $category->getIsActive()) {
            try {
                $categoryUrl = $this->getCategoryUrl($category);
                $this->addButton(
                    'open_category_page',
                    [
                        'label' => __('Open category page'),
                        'on_click' => 'window.open("' . $categoryUrl . '")',
                        'class' => 'view open_category_page'
                    ]
                );
            } catch (NoSuchEntityException $e) {
                // Do nothing
            }
        }
    }

    /**
     * Get frontend category url.
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getCategoryUrl($category)
    {
        $store = $this->_request->getParam('store');
        if (!$store) {
            $this->emulation->startEnvironmentEmulation(null, Area::AREA_FRONTEND, true);
            $categoryUrl = $this->categoryRepository->get($category->getId())->getUrl();
            $this->emulation->stopEnvironmentEmulation();
            return $categoryUrl;
        }
        return $this->categoryRepository->get($category->getId(), $store)->getUrl();
    }
}