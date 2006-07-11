<?php
// vim: foldmethod=marker
/**
 *	Ethna_Plugin_Logwriter_File.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

// {{{ Ethna_Plugin_Logwriter_File
/**
 *	�������ϥ��饹(File)
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_Plugin_Logwriter_File extends Ethna_Plugin_Logwriter
{
	/**#@+
	 *	@access	private
	 */

	/**	@var	int		�����ե�����ϥ�ɥ� */
	var	$fp;

	/**#@-*/

	/**
	 *	Ethna_Plugin_Logwriter_File���饹�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 */
	function Ethna_Plugin_Logwriter_File()
	{
		$this->fp = null;
	}

	/**
	 *	�������ץ��������ꤹ��
	 *
	 *	@access	public
	 *	@param	int		$option     �������ץ����(LOG_FILE,LOG_FUNCTION...)
	 */
	function setOption($option)
	{
		parent::setOption($option);
        
        if (isset($option['file'])) {
            $this->file = $option['file'];
        } else {
            $this->file = $this->_getLogFile();
        }
	}

	/**
	 *	�������Ϥ򳫻Ϥ���
	 *
	 *	@access	public
	 */
	function begin()
	{
		$this->fp = fopen($this->file, 'a');
	}

	/**
	 *	��������Ϥ���
	 *
	 *	@access	public
	 *	@param	int		$level		������٥�(LOG_DEBUG, LOG_NOTICE...)
	 *	@param	string	$message	������å�����(+����)
	 */
	function log($level, $message)
	{
		if ($this->fp == null) {
			return;
		}

		$prefix = strftime('%Y/%m/%d %H:%M:%S ') . $this->ident;
		if (array_key_exists("pid", $this->option)) {
			$prefix .= sprintf('[%d]', getmypid());
		}
		$prefix .= sprintf('(%s): ', $this->_getLogLevelName($level));
		if (array_key_exists("function", $this->option) ||
            array_key_exists("pos", $this->option)) {
			$tmp = "";
			$bt = $this->_getBacktrace();
			if ($bt && array_key_exists("function", $this->option) && $bt['function']) {
				$tmp .= $bt['function'];
			}
			if ($bt && array_key_exists("pos", $this->option) && $bt['pos']) {
				$tmp .= $tmp ? sprintf('(%s)', $bt['pos']) : $bt['pos'];
			}
			if ($tmp) {
				$prefix .= $tmp . ": ";
			}
		}
		fwrite($this->fp, $prefix . $message . "\n");

		return $prefix . $message;
	}

	/**
	 *	�������Ϥ�λ����
	 *
	 *	@access	public
	 */
	function end()
	{
		if ($this->fp) {
			fclose($this->fp);
			$this->fp = null;
		}
	}

	/**
	 *	�����ե�����ν񤭽Ф�����������(�����ե�����ƥ���
	 *	LOG_FILE�����ꤵ��Ƥ�����Τ�ͭ��)
	 *
	 *	�����ե�����ν񤭽Ф�����ѹ����������Ϥ��Υ᥽�åɤ�
	 *	�����С��饤�ɤ��ޤ�
	 *
	 *	@access	protected
	 *	@return	string	�����ե�����ν񤭽Ф���
	 */
	function _getLogFile()
	{
        $controller =& Ethna_Controller::getInstance();

        if (array_key_exists("dir", $this->option)) {
            $dir = $this->option['dir'];
        } else {
			$dir = $controller->getDirectory('log');
        }

		return sprintf('%s/%s.log',
			$dir,
			strtolower($controller->getAppid())
		);
	}
}
// }}}
?>