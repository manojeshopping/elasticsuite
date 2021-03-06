<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile_ElasticSuiteCore
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ElasticSuiteCore\Search\Adapter\ElasticSuite\Response;

/**
 * ES search document.
 *
 * @category Smile
 * @package  Smile_ElasticSuiteCore
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Document extends \Magento\Framework\Api\Search\Document
{
    /**
     * @var string
     */
    const SCORE_DOC_FIELD_NAME  = "_score";

    /**
     * @var string
     */
    const SOURCE_DOC_FIELD_NAME = "_source";

    /**
     * Return search document score.
     *
     * @return float
     */
    public function getScore()
    {
        return (float) $this->_get(self::SCORE_DOC_FIELD_NAME);
    }

    /**
     * Document source data.
     *
     * @return array
     */
    public function getSource()
    {
        return $this->_get(self::SOURCE_DOC_FIELD_NAME);
    }
}
