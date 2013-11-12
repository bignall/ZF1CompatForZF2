<?php
/**
 * Based on Zend\Session\SaveHandler\DbTableGateway
 *
 * This allows you to use ZF1 session db handling and ZF2 session db handling together
 *
 * Note: ZF2 stores the session name, ZF1 does not.  This is the primary difference between the two.
 * Therefore, this implementation ignores the session name for reading and only uses the ID.
 * It does store the session name so it is available after the first time the session is updated.
 *
 */

namespace Zf1CompatForZf2\Zend\Session\SaveHandler;

use Zend\Db\TableGateway\TableGateway;
use Zend\Session\SaveHandler\DbTableGateway;

/**
 * DB Table Gateway session save handler
 */
class Zf1DbTableGateway extends DbTableGateway
{
    /**
     * Read session data
     *
     * @param string $id
     * @return string
     */
    public function read($id)
    {
        $rows = $this->tableGateway->select(array(
            $this->options->getIdColumn()   => $id,
//            $this->options->getNameColumn() => $this->sessionName,  -- ignore the name column because ZF1 doesn't use it
        ));

        if ($row = $rows->current()) {
            if ($row->{$this->options->getModifiedColumn()} +
                $row->{$this->options->getLifetimeColumn()} > time()) {
                return $row->{$this->options->getDataColumn()};
            }
            $this->destroy($id);
        }
        return '';
    }

    /**
     * Write session data
     *
     * @param string $id
     * @param string $data
     * @return bool
     */
    public function write($id, $data)
    {
        $data = array(
            $this->options->getModifiedColumn() => time(),
            $this->options->getDataColumn()     => (string) $data,
            $this->options->getNameColumn()     => $this->sessionName,
        );

        $rows = $this->tableGateway->select(array(
            $this->options->getIdColumn()   => $id,
//            $this->options->getNameColumn() => $this->sessionName,
        ));

        if ($row = $rows->current()) {
            return (bool) $this->tableGateway->update($data, array(
                $this->options->getIdColumn()   => $id,
//                $this->options->getNameColumn() => $this->sessionName,
            ));
        }
        $data[$this->options->getLifetimeColumn()] = $this->lifetime;
        $data[$this->options->getIdColumn()]       = $id;
        $data[$this->options->getNameColumn()]     = $this->sessionName;
        return (bool) $this->tableGateway->insert($data);
    }

    /**
     * Destroy session
     *
     * @param  string $id
     * @return bool
     */
    public function destroy($id)
    {
        return (bool) $this->tableGateway->delete(array(
            $this->options->getIdColumn()   => $id,
//            $this->options->getNameColumn() => $this->sessionName,
        ));
    }

}
