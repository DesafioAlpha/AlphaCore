<?php
/**
 * Desafio Alpha - AlphaCore
 * 
 * LICENÇA:
 * 
 * Este software é distribuído sob a licença GNU General Public License - Versão 3. 
 * O conteúdo desta licença está disponível no arquivo LICENSE.txt e nos endereços web 
 * http://doc.desafioalpha.com.br/legal e http://www.gnu.org/licenses/gpl.html
 * 
 * Qualquer dúvida sobre este arquivo fonte e como usá-lo refira-se à 
 * documentação anexada à este pacote de software ou envie um email 
 * para dev@desafioalpha.com.br
 * 
 * ESTE SOFTWARE É DISPONIBILIZADO 'NA FORMA COMO ESTÁ', O AUTOR NÃO OFERECE 
 * NENHUMA GARANTIA, EXPLÍCITA OU IMPLÍCITA, SOBRE PRECISÃO E CONFIABILIDADE.
 * SINTA-SE LIVRE PARA VER, MODIFICAR E REDISTRIBUIR, SOB AS CONDIÇÕES DA 
 * LICENÇA APLICADA. 
 *
 * @category     Desafio Alpha
 * @package      DA_Lib
 * @file         Files.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

/**
 * Métodos para tratamento de solicitações de upload 
 *
 * @package      DA_Lib
 * @subpackage   Files
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 */
class DA_Lib_Files
{   
    /** Contextos */
    const MEDIA_IMAGE = 1;
    const MEDIA_AUDIO = 2;
    const MEDIA_VIDEO = 3;
    const MEDIA_DOC   = 4;
    const MEDIA_OTHER = 5;
    
    /**
     * Remove path information and dots around the filename, to prevent uploading
     * into different directories or replacing hidden system files.
     * 
     * @see http://blueimp.github.com/jQuery-File-Upload/
     * 
     * @param string $name Filename
     * @param string $type File mime-type
     * 
     * @return string
     */
    static private function _trimFileName($name, $type) {

        // Also remove control characters and spaces (\x00..\x20) around the filename:
        $file_name = trim(basename(stripslashes($name)), ".\x00..\x20");
        // Add missing file extension for known image types:
        if (strpos($file_name, '.') === false &&
        preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
            $file_name .= '.'.$matches[1];
        }
        return $file_name;
    }

    /**
     * Diretivas para autorização de upload pelo tipo de arquivo
     * 
     * @param string $filePath Arquivo temporário transferido
     * @param int    $context  Contexto da solicitação
     * 
     * @return boolean
     */
    static private function _isAllowed($filePath, $context)
    {
        switch ($context){
            case self::MEDIA_IMAGE :
                /* Verifica se é uma imagem */
                $type = exif_imagetype($filePath); 
                return ($type && $type <= 3); // GIF(1), JPG(2) ou PNG(3)
                break;
            default:
                return true;        
        }
    }

    /**
     * Salva um arquivo de uma operação de upload.
     * 
     * @see http://blueimp.github.com/jQuery-File-Upload/
     * 
     * @param string $upload     Caminho para o arquivo temporário transferido
     * @param string $fileName   Nome do arquivo transferido
     * @param string $fileDir    Diretório para o qual o arquivo será salvo
     * @param int    $size       Tamanho (em bytes) transferidos
     *         
     * @throws Exception         Se o arquivo não tiver uma extensão válida
     * @return stdClass          Informações sobre o arquivo salvo
     */
    static private function _save($upload, $fileName, $fileDir, $size)
    {        
        $type = mime_content_type($upload);
        
        $file = new stdClass();
        $file->name = self::_trimFileName($fileName, $type);
        $file->size = intval($size, 10);
        $file->type = $type;
        
        if(!$file->extension = strtolower(substr(strrchr($file->name, '.'), 1))){
            throw new Exception('File extension is required');
        }
        
        if ($file->name) {
            
            $filePath =  $fileDir . $file->name;
            
            // Permite retomada da transferência
            $resumeUpload = is_file($filePath) && $file->size > filesize($filePath);
            
            clearstatcache();
            
            if ($upload && is_uploaded_file($upload)) { // O arquivo foi enviado por POST
                
                
                if ($resumeUpload) { // Continua a transferência do arquivo
                    file_put_contents(
                        $filePath,
                        fopen($upload, 'r'),
                        FILE_APPEND
                    );
                } else {
                    move_uploaded_file($upload, $filePath);
                }
                
            } else { // O arquivo foi enviado por PUT

                file_put_contents(
                    $filePath,
                    fopen('php://input', 'r'),
                    $resumeUpload ? FILE_APPEND : 0
                );
            }
            
            $fileSize = filesize($filePath);
            
            if ($fileSize === $file->size) { // Transferência concluída
                $file->path = $filePath;         
            }
            
            $file->size = $fileSize;

        } else {
            $file->error = $error;
        }
        
        return $file;
    }

    /**
     * Cria uma miniatura com a dimensão máxima passada
     * 
     * @param string $filePath  Caminho do arquivo original
     * @param int $maxWidth     Largura máxima da miniatura
     * @param int $maxHeight    Altura máxima da miniatura
     * 
     * @return boolean
     */
    static private function _createThumbnail($filePath, $maxWidth, $maxHeight) {
        
        /* Informações sobre a imagem */
        list($imgWidth, $imgHeight, $imageType) = getimagesize($filePath);
        
        if (!$imgWidth || !$imgHeight) {
            return false;
        }
        
        $fileInfo = pathinfo($filePath);
        $newFilePath = $fileInfo['dirname'] . '/thumb/' . $fileInfo['basename'];
        
        /* Define as dimensões da miniatura */        
        $scale = min(1, min(
            $maxWidth  / $imgWidth,
            $maxHeight / $imgHeight
        ));

        if($scale == 1){ // Se a imagem original é menor ou igual ao tamanho da miniatura, apenas a copia
            copy($filePath, $newFilePath);
            return true;
        }
        
        $newWidth  = $imgWidth  * $scale;
        $newHeight = $imgHeight * $scale;
        
        $newImg = @imagecreatetruecolor($newWidth, $newHeight);
        
        /* Procede de acordo com o tipo de imagem */
        switch ($imageType) {
            
            case 1: // GIF
            
                @imagecolortransparent($newImg, @imagecolorallocate($newImg, 0, 0, 0));
                $originalImg = @imagecreatefromgif($filePath);
                $writeImage = 'imagegif';
                break;

            case 2 : // JPG
                
                $originalImg = @imagecreatefromjpeg($filePath);
                $writeImage = 'imagejpeg';
                break;

            case 3: // PNG
                
                @imagecolortransparent($newImg, @imagecolorallocate($newImg, 0, 0, 0));
                @imagealphablending($newImg, false);
                @imagesavealpha($newImg, true);
                $originalImg = imagecreatefrompng($filePath);
                $writeImage = 'imagepng';
                break;
                
            default:
                return false;
        }
        
        /* Verifica o processo e grava a miniatura */
        $success = $originalImg && @imagecopyresampled(
            $newImg,
            $originalImg,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $imgWidth,
            $imgHeight
        ) && $writeImage($newImg, $newFilePath);
        
        /* Libera memória */
        imagedestroy($originalImg);
        imagedestroy($newImg);
        
        return $success;
    }
    
    /**
     * Apaga um arquivo do sistema de arquivos.
     * 
     * @param  string $path
     * 
     * @return boolean
     */
    static private function _delete($path)
    {
        if(is_file($path) && $path !== '.'){
            return unlink($path);
        }
    } 
    
    /**
     * Salva no sistema de arquivos e adiciona recurso de mídia para o desafio passado
     * 
     * @param string $questionId  Id do desafio
     * @param string $upload      Caminho temporário do arquivo transferido
     * @param string $fileName    Nome original do arquivo, reportado pelo cliente 
     * @param int    $size        Tamanho (em bytes) do arquivo, reportado pelo cliente
     * @param int    $context     Contexto da transferência     
     *        
     * @return boolean|string     Falso se falhar, caminho relativo no sistema de arquivos
     *                            do arquivo transferido se proceder com sucesso
     */
    static public function saveQuestionMedia($questionId, $upload, $fileName, $size, $context)
    {
        /* Define o caminho para salvar o arquivo */
        $path = 'questions/';
        switch ($context) {
            case self::MEDIA_IMAGE :
                $path .= 'images/';
                break;
            case self::MEDIA_AUDIO :
                $path .= 'audio/';
                break;
            case self::MEDIA_VIDEO :
                $path .= 'videos/';
                break;
            case self::MEDIA_DOC :
                $path .= 'other/';
                break;
            case self::MEDIA_OTHER :
                $path .= 'other/';
                break;
            default:
                return false;
        }
    
        if(self::_isAllowed($upload, $context) && $fileInfo = self::_save($upload, $fileName, STATIC_PATH . $path, $size)){
    
            /* Quando a transferência estiver completa, registra o recurso no BD e
             * renomeia o arquivo.
            */
            $newFileName = $path . DA_Lib_Math::hash() . '.' . $fileInfo->extension;
            $newFilePath = STATIC_PATH . $newFileName;
    
            rename($fileInfo->path, $newFilePath);
    
            /* Cria uma miniatura para a imagem */
            if(($context == self::MEDIA_IMAGE) && !self::_createThumbnail($newFilePath, 200, 200)){
                return false;
            }
    
            $questionMedia = new DA_Model_Dbtable_QuestionMedia();
            $questionMedia->addMedia($context, $newFileName, $questionId);
    
            return $newFileName;
        }
        
        return false;
    }
    
    /**
     * Apaga os recursos de mídia especificados do sistema de arquivos.
     * 
     * @param  string $files  Lista de arquivos a apagar
     * 
     * @return boolean 
     */
    static public function deleteQuestionMedia($files)
    {        
        /* Apaga todos os arquivos da lista */
        foreach ($files as $filename){
            $path = STATIC_PATH . $filename['path'];
            if($filename['media_type'] == self::MEDIA_IMAGE){ // Apaga a miniatura
                $pathInfo = pathinfo($path);
                $thumbPath = $pathInfo['dirname'] . '/thumb/' . $pathInfo['basename'];
                self::_delete($thumbPath);
            }
            self::_delete($path);
        }
        
        return true;
    }
}