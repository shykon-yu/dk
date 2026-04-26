<?php
namespace App\Services\Admin;

use App\Services\Admin\Goods\GoodsSeasonService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

abstract class BaseService
{
    protected string $modelClass;
    protected string $cacheKey;
    protected int $cacheTtl = 180 * 24 * 60 * 60;
    protected string $cachePrefix = 'admin_';

    // 图片上传相关配置（可被子类重写）
    protected array $uploadConfig = [
        'allowed_types' => ['jpg', 'png', 'gif', 'jpeg', 'ico'], // 允许的图片类型
        'max_size'      => 20 * 1024 * 1024, // 20MB
        'base_dir'      => 'uploads/images', // 基础存储目录
        'quality'       => 90, // 图片质量
        'thumb_width'   => 800, // 缩略图宽度
    ];

    public function store(array $data): bool
    {
        try {
            $this->getModelClass()::create($data);
            $this->clearCache();
            return true;
        } catch (\Exception $e) {
            throw new \Exception($this->formatMsg('新增', $e->getMessage()));
        }
    }

    public function update(Model $model, array $data): bool
    {
        try {
            $model->update($data);
            $this->clearCache();
            return true;
        } catch (\Exception $e) {
            throw new \Exception($this->formatMsg('修改', $e->getMessage()));
        }
    }

    public function destroy(Model $model): bool
    {
        try {
            $model->delete();
            $this->clearCache();
            return true;
        } catch (\Exception $e) {
            throw new \Exception($this->formatMsg('删除', $e->getMessage()));
        }
    }

    public function batchDestroy(array $ids): bool
    {
        try {
            $currentLevel = Auth::user()->roles->sortBy('level')->first()?->level ?? 999;
            if( $currentLevel > 1 ){
                $datas = $this->getModelClass()::whereIn('id', $ids)->get();
                foreach ($datas as $data) {
                    $targetLevel = $data->creator->roles->sortBy('level')->first()?->level ?? 999;
                    $created_user_id = $data->creator->id ?? null;
                    if ($currentLevel > $targetLevel) {
                        if( !is_null($created_user_id) && Auth::id() !== $created_user_id ){
                            throw new \Exception('存在无法删除的数据');
                        }
                    }
                }
            }
            $this->getModelClass()::destroy($ids);
            $this->clearCache();
            return true;
        } catch (\Exception $e) {
            throw new \Exception($this->formatMsg('批量删除', $e->getMessage()));
        }
    }

    /**
     * 获取除了软删除之外的所有数据
     */
    public function getAllWithoutTrashed()
    {
        return $this->modelClass::query()
            ->orderBy('sort', 'asc')
            ->get();
    }

    public function getFullCacheKey(): string
    {
        return $this->cachePrefix . $this->cacheKey;
    }

    public function clearCache(): void
    {
        Cache::forget($this->getFullCacheKey());
    }

    abstract public function getCacheAll();

    public function getModelClass()
    {
        return $this->modelClass;
    }

    // 统一异常消息格式化
    protected function formatMsg(string $action, string $detail): string
    {
        $modelName = class_basename($this->modelClass);
        return "{$action}{$modelName}失败：{$detail}";
    }

    protected function getPerPage()
    {
        return (new $this->modelClass)->getPerPage();
    }

    // 通用内存分页
    protected function paginateCacheData($collection, array $params, int $perPage = 20): LengthAwarePaginator
    {
        $page = $params['page'] ?? 1;
        $paginator = new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page
        );
        $paginator->setPath(request()->url());
        return $paginator;
    }

    public function changeStatus(Model $model, int $status): object
    {
        try {
            $model->status = $status;
            $model->save();
            $this->clearCache();
            return $model;
        } catch (\Exception $e) {
            throw new \Exception($this->formatMsg('状态修改', $e->getMessage()));
        }
    }

    public function changeStar(Model $model, int $star): object
    {
        try {
            $model->is_star = $star;
            $model->save();
            $this->clearCache();
            return $model;
        } catch (\Exception $e) {
            throw new \Exception($this->formatMsg('星标修改', $e->getMessage()));
        }
    }

    /**
     * 通用图片上传方法（支持主图+缩略图）
     * @param UploadedFile $file 上传的文件对象
     * @param string $module 模块名（如 goods、user，用于区分存储目录）
     * @return array 包含主图/缩略图路径的数组
     * @throws \Exception
     */
    public function uploadImage(UploadedFile $file, string $module = 'common'): array
    {
        try {
            // 1. 基础验证
            $this->validateUploadFile($file);

            // 2. 构建目录
            $dateDir = date('Y-m-d');
            // 主图相对/绝对目录
            $mainRelativeDir = "{$this->uploadConfig['base_dir']}/{$module}/main/{$dateDir}";
            $mainAbsDir = public_path($mainRelativeDir);
            // 缩略图相对/绝对目录
            $thumbRelativeDir = "{$this->uploadConfig['base_dir']}/{$module}/thumb/{$dateDir}";
            $thumbAbsDir = public_path($thumbRelativeDir);

            // 3. 创建目录
            File::makeDirectory($mainAbsDir, 0755, true, true);
            File::makeDirectory($thumbAbsDir, 0755, true, true);

            // 4. 生成文件名（统一转jpg）
            $fileName = time() . rand(10000, 99999) . '.jpg';

            // 5. 生成主图
            $mainAbsPath = "{$mainAbsDir}/{$fileName}";
            $img = imagecreatefromstring(file_get_contents($file->getPathname()));
            imagejpeg($img, $mainAbsPath, $this->uploadConfig['quality']);

            // 6. 生成缩略图
            $thumbAbsPath = "{$thumbAbsDir}/{$fileName}";
            $this->generateThumbnail($img, $thumbAbsPath);

            // 7. 销毁资源
            imagedestroy($img);

            // 8. 返回路径（相对public目录）
            return [
                'main_url' => "{$mainRelativeDir}/{$fileName}",
                'thumb_url' => "{$thumbRelativeDir}/{$fileName}",
                'file_name' => $fileName,
                'id' => time() // 兼容前端ID字段
            ];

        } catch (\Exception $e) {
            throw new \Exception($this->formatMsg('图片上传', $e->getMessage()));
        }
    }

    /**
     * 验证上传文件（类型/大小）
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function validateUploadFile(UploadedFile $file): void
    {
        // 验证文件是否存在
        if (!$file->isValid()) {
            throw new \Exception('上传文件无效');
        }

        // 验证大小
        if ($file->getSize() > $this->uploadConfig['max_size']) {
            $maxSize = $this->uploadConfig['max_size'] / 1024 / 1024;
            throw new \Exception("图片大小不能超过{$maxSize}MB");
        }

        // 验证类型
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $this->uploadConfig['allowed_types'])) {
            $types = implode(',', $this->uploadConfig['allowed_types']);
            throw new \Exception("只支持上传{$types}类型的图片");
        }
    }

    /**
     * 生成缩略图
     * @param resource $img 图片资源
     * @param string $savePath 保存路径
     */
    protected function generateThumbnail($img, string $savePath): void
    {
        $width = imagesx($img);
        $height = imagesy($img);

        // 计算等比例高度
        $newWidth = $this->uploadConfig['thumb_width'];
        $newHeight = (int)(($height / $width) * $newWidth);

        // 创建缩略图资源
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled(
            $thumb, $img,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $width, $height
        );

        // 保存缩略图
        imagejpeg($thumb, $savePath, $this->uploadConfig['quality']);
        imagedestroy($thumb);
    }

    protected function unlinkImage(string $path): bool
    {
        if (!$path) return false;
        $file = public_path($path);
        if (file_exists($file)) {
            @unlink($file);
        }
        return true;
    }
}
