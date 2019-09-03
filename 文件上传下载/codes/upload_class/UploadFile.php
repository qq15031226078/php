<?php
/**
 * Created by PhpStorm.
 * User: JasonLee
 * Date: 2018/11/11
 * Time: 22:01
 */

class UploadFile
{

    /**
     *
     */
    const UPLOAD_ERROR = [
        UPLOAD_ERR_INI_SIZE => '文件大小超出了php.ini当中的upload_max_filesize的值',
        UPLOAD_ERR_FORM_SIZE => '文件大小超出了MAX_FILE_SIZE的值',
        UPLOAD_ERR_PARTIAL => '文件只有部分被上传',
        UPLOAD_ERR_NO_FILE => '没有文件被上传',
        UPLOAD_ERR_NO_TMP_DIR => '找不到临时目录',
        UPLOAD_ERR_CANT_WRITE => '写入磁盘失败',
        UPLOAD_ERR_EXTENSION => '文件上传被扩展阻止',
    ];

    /**
     * @var
     */
    protected $field_name;

    /**
     * @var string
     */
    protected $destination_dir;

    /**
     * @var array
     */
    protected $allow_mime;

    /**
     * @var array
     */
    protected $allow_ext;

    /**
     * @var
     */
    protected $file_org_name;

    /**
     * @var
     */
    protected $file_type;

    /**
     * @var
     */
    protected $file_tmp_name;

    /**
     * @var
     */
    protected $file_error;

    /**
     * @var
     */
    protected $file_size;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var
     */
    protected $extension;

    /**
     * @var
     */
    protected $file_new_name;

    /**
     * @var float|int
     */
    protected $allow_size;

    /**
     * UploadFile constructor.
     * @param $keyName
     * @param string $destinationDir
     * @param array $allowMime
     * @param array $allowExt
     * @param float|int $allowSize
     */
    public function __construct($keyName, $destinationDir = './uploads', $allowMime = ['image/jpeg', 'image/gif'], $allowExt = ['gif', 'jpeg'], $allowSize = 2*1024*1024)
    {
        $this->field_name = $keyName;
        $this->destination_dir = $destinationDir;
        $this->allow_mime = $allowMime;
        $this->allow_ext = $allowExt;
        $this->allow_size = $allowSize;
    }

    /**
     * @param $destinationDir
     */
    public function setDestinationDir($destinationDir)
    {
        $this->destination_dir = $destinationDir;
    }

    /**
     * @param $allowMime
     */
    public function setAllowMime($allowMime)
    {
        $this->allow_mime = $allowMime;
    }

    /**
     * @param $allowExt
     */
    public function setAllowExt($allowExt)
    {
        $this->allow_ext = $allowExt;
    }

    /**
     * @param $allowSize
     */
    public function setAllowSize($allowSize)
    {
        $this->allow_size = $allowSize;
    }

    /**
     * @return bool
     */
    public function upload()
    {
        // 判断是否为多文件上传
        $files = [];
        if (is_array($_FILES[$this->field_name]['name'])) {
            foreach($_FILES[$this->field_name]['name'] as $k => $v) {
                $files[$k]['name'] = $v;
                $files[$k]['type'] = $_FILES[$this->field_name]['type'][$k];
                $files[$k]['tmp_name'] = $_FILES[$this->field_name]['tmp_name'][$k];
                $files[$k]['error'] = $_FILES[$this->field_name]['error'][$k];
                $files[$k]['size'] = $_FILES[$this->field_name]['size'][$k];
            }
        } else {
            $files[] = $_FILES[$this->field_name];
        }

        foreach($files as $key => $file) {
            // 接收$_FILES参数
            $this->setFileInfo($key, $file);

            // 检查错误
            $this->checkError($key);

            // 检查MIME类型
            $this->checkMime($key);

            // 检查扩展名
            $this->checkExt($key);

            // 检查文件大小
            $this->checkSize($key);

            // 生成新的文件名称
            $this->generateNewName($key);

            if (count((array)$this->getError($key)) > 0) {
                continue;
            }
            // 移动文件
            $this->moveFile($key);
        }
        if (count((array)$this->errors) > 0) {
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getError($key)
    {
        return $this->errors[$key];
    }

    /**
     *
     */
    protected function setFileInfo($key, $file)
    {
        // $_FILES  name type temp_name error size
        $this->file_org_name[$key] = $file['name'];
        $this->file_type[$key] = $file['type'];
        $this->file_tmp_name[$key] = $file['tmp_name'];
        $this->file_error[$key] = $file['error'];
        $this->file_size[$key] = $file['size'];
    }


    /**
     * @param $key
     * @param $error
     */
    protected function setError($key, $error)
    {
        $this->errors[$key][] = $error;
    }


    /**
     * @param $key
     * @return bool
     */
    protected function checkError($key)
    {
        if ($this->file_error > UPLOAD_ERR_OK) {
            switch($this->file_error) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                case UPLOAD_ERR_PARTIAL:
                case UPLOAD_ERR_NO_FILE:
                case UPLOAD_ERR_NO_TMP_DIR:
                case UPLOAD_ERR_CANT_WRITE:
                case UPLOAD_ERR_EXTENSION:
                    $this->setError($key, self::UPLOAD_ERROR[$this->file_error]);
                    return false;
            }
        }
        return true;
    }


    /**
     * @param $key
     * @return bool
     */
    protected function checkMime($key)
    {
        if (!in_array($this->file_type[$key], $this->allow_mime)) {
            $this->setError($key, '文件类型' . $this->file_type[$key] . '不被允许!');
            return false;
        }
        return true;
    }


    /**
     * @param $key
     * @return bool
     */
    protected function checkExt($key)
    {
        $this->extension[$key] = pathinfo($this->file_org_name[$key], PATHINFO_EXTENSION);
        if (!in_array($this->extension[$key], $this->allow_ext)) {
            $this->setError($key, '文件扩展名' . $this->extension[$key] . '不被允许！');
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function checkSize($key)
    {
        if ($this->file_size[$key] > $this->allow_size) {
            $this->setError($key, '文件大小' . $this->file_size[$key] . '超出了限定大小' . $this->allow_size);
            return false;
        }
        return true;
    }


    /**
     * @param $key
     */
    protected function generateNewName($key)
    {
        $this->file_new_name[$key] = uniqid() . '.' . $this->extension[$key];
    }


    /**
     * @param $key
     * @return bool
     */
    protected function moveFile($key)
    {
        if (!file_exists($this->destination_dir)) {
            mkdir($this->destination_dir, 0777, true);
        }
        $newName = rtrim($this->destination_dir, '/') . '/' . $this->file_new_name[$key];
        if (is_uploaded_file($this->file_tmp_name[$key]) && move_uploaded_file($this->file_tmp_name[$key], $newName)) {
            return true;
        }
        $this->setError($key, '上传失败！');
        return false;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->file_new_name;
    }

    /**
     * @return string
     */
    public function getDestinationDir()
    {
        return $this->destination_dir;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return mixed
     */
    public function getFileSize()
    {
        return $this->file_size;
    }

}