<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/15
 * Time: 下午4:51
 */

namespace backend\baipao123\layuiAdm\pageList;


class PageList
{
    const DEFAULT_PAGE_SIZE = 10;

    private $_pageSize = self::DEFAULT_PAGE_SIZE;

    private $_itemCount = 0;

    private $_currentPage;

    /**
     * Constructor.
     * @param integer $itemCount total number of items.
     */
    public function __construct($itemCount=0)
    {
        $this->setItemCount($itemCount);
    }

    /**
     * @return int
     */
    public function getItemCount() {
        return $this->_itemCount;
    }

    /**
     * @param int $itemCount
     */
    public function setItemCount($itemCount) {
        $this->_itemCount = $itemCount;
    }

    /**
     * @return int
     */
    public function getPageSize() {
        return $this->_pageSize;
    }

    /**
     * @param int $pageSize
     */
    public function setPageSize($pageSize) {
        $this->_pageSize = $pageSize;
    }

    /**
     * @return mixed
     */
    public function getCurrentPage() {
        return $this->_currentPage;
    }

    /**
     * @param mixed $currentPage
     */
    public function setCurrentPage($currentPage) {
        $this->_currentPage = $currentPage;
    }

    /**
     * @return integer number of pages
     */
    public function getPageCount()
    {
        return (int)(($this->_itemCount+$this->_pageSize-1)/$this->_pageSize);
    }

    /**
     * @return integer the offset of the data. This may be used to set the
     * OFFSET value for a SQL statement for fetching the current page of data.
     * @since 1.1.0
     */
    public function getOffset()
    {
        return $this->getCurrentPage()*$this->getPageSize();
    }

    /**
     * @return integer the limit of the data. This may be used to set the
     * LIMIT value for a SQL statement for fetching the current page of data.
     * This returns the same value as {@link pageSize}.
     * @since 1.1.0
     */
    public function getLimit()
    {
        return $this->getPageSize();
    }
}