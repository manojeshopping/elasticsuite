<?php
/**
 * DISCLAIMER :
 *
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 * @category  Smile_ElasticSuite
 * @package   Smile_ElasticSuiteThesaurus
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ElasticSuiteThesaurus\Model\ResourceModel\Indexer;

use Smile\ElasticSuiteThesaurus\Model\Index as ThesaurusIndex;
use Smile\ElasticSuiteThesaurus\Api\Data\ThesaurusInterface;

/**
 * Thesaurus indexer resource model.
 *
 * @category Smile_ElasticSuite
 * @package  Smile_ElasticSuiteThesaurus
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Thesaurus extends \Smile\ElasticSuiteThesaurus\Model\ResourceModel\Thesaurus
{
    /**
     * Build a list of synonyms into the Lucene format (foo,bar).
     *
     * @param int $storeId Store id.
     *
     * @return string[]
     */
    public function getSynonyms($storeId)
    {
        $connection = $this->getConnection();
        $select     = $this->getBaseSelect($storeId, ThesaurusInterface::TYPE_SYNONYM);

        $select->columns(['terms' => new \Zend_Db_Expr('GROUP_CONCAT(terms.term)')]);

        return $connection->fetchCol($select);
    }

    /**
     * Build a list of expansions into the Lucene format (foo => bar).
     *
     * @param int $storeId Store id.
     *
     * @return string[]
     */
    public function getExpansions($storeId)
    {
        $connection = $this->getConnection();
        $select     = $this->getBaseSelect($storeId, ThesaurusInterface::TYPE_EXPANSION);

        $select->join(
            ['expanded_terms' => ThesaurusInterface::REFERENCE_TABLE_NAME],
            'expanded_terms.term_id = terms.term_id AND expanded_terms.thesaurus_id = terms.thesaurus_id',
            []
        );

        $select->columns(['terms' => new \Zend_Db_Expr('CONCAT(expanded_terms.term, " => " ,GROUP_CONCAT(terms.term))')]);

        return $connection->fetchCol($select);
    }

    /**
     * Base select for thesaurus terms select by store id.
     *
     * @param int    $storeId Store id.
     * @param string $type    Type of thesaurus (synonym or expansion).
     *
     * @return \Magento\Framework\DB\Select
     */
    private function getBaseSelect($storeId, $type)
    {
        $connection = $this->getConnection();
        $select     = $connection->select();

        $select->from(['thesaurus' => $this->getMainTable()], [])
            ->join(['terms' => ThesaurusInterface::EXPANSION_TABLE_NAME], 'thesaurus.thesaurus_id = terms.thesaurus_id', [])
            ->join(['store' => ThesaurusInterface::STORE_TABLE_NAME], 'store.thesaurus_id = thesaurus.thesaurus_id', [])
            ->group(['thesaurus.thesaurus_id', 'terms.term_id'])
            ->where("thesaurus.type = ?", $type)
            ->where('store.store_id IN (?)', [0, $storeId]);

        return $select;
    }
}
