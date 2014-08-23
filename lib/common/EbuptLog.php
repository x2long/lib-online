<?php
/**
 * @package lib.common
 * @author xuxiaolong <xuxiaolong@ebupt.com>
 */

/**
 * class EbuptApp.
 * 
 * @author pangbo
 */
class EbuptLog
{
	/**
	 * 
	 * @param string $message
	 * @param string $level
	 * @param string $category
	 * @param string $user
	 */
	public function log($message, $level='info', $category='application', $user=null) {
		if ($user == null) {
			$user = Yii::app()->user->name;
		}
		$message = "[ $user ] ".$message;
		Yii::log($message, $level, $category);
	}
	
	/**
     * ��Ӳ�����־
     * (�÷����������FormModel��add_operation_log())
     * @param string $action �����Ķ���
     * @param string $message ��־��Ϣ
     * @return boolean ������־����Ƿ�ɹ�
     */
    static public function addOperationLog($action, $message)
    {
        // ������־����
        $logMessage = array(
                'action' => $action,
                'info' => $message,
                'owner' => Yii::app()->user->name,
                'ip' => Yii::app()->request->getUserHostAddress(),
                'time' => date("Y-m-d H:i:s"),
        );
        // ����־������ݿ�
        try {
    	    $logRecord = new LogRecord;
            $logRecord->attributes = $logMessage;
            if ($logRecord->save())
                return true;
            throw new Exception();
        } catch(Exception $e) {
            Yii::log('Failed logging to db!', 'error');
            return false;
        }
    }
}
