<?php

namespace App\Compat\CodeIgniter\HTTP;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CiUploadedFile
{
    private bool $moved = false;

    public function __construct(private readonly ?UploadedFile $file)
    {
    }

    public function isValid(): bool
    {
        return $this->file instanceof UploadedFile && $this->file->isValid();
    }

    public function hasMoved(): bool
    {
        return $this->moved;
    }

    public function getRandomName(): string
    {
        $ext = $this->file?->getClientOriginalExtension() ?: 'bin';
        return Str::random(24) . '.' . strtolower($ext);
    }

    public function move(string $path, ?string $name = null): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $name ??= $this->file->getClientOriginalName();
        $this->file->move($path, $name);
        $this->moved = true;

        return true;
    }

    public function getExtension(): ?string
    {
        return $this->file?->getClientOriginalExtension();
    }

    public function getTempName(): ?string
    {
        return $this->file?->getRealPath();
    }

    public function getClientName(): ?string
    {
        return $this->file?->getClientOriginalName();
    }
}
