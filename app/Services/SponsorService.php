<?php
// app/Services/SponsorService.php

namespace App\Services;

use App\Models\Sponsor;
use Illuminate\Http\UploadedFile;

class SponsorService
{
    public function getAllSponsors()
    {
        return Sponsor::all();
    }

    public function createSponsor(array $data)
    {
        return Sponsor::create($data);
    }

    public function updateSponsor(Sponsor $sponsor, array $data)
    {
        $sponsor->update($data);
        return $sponsor->wasChanged();
    }

    public function updateSponsorWithImage(Sponsor $sponsor, array $data, UploadedFile $image)
    {
        $data['image'] = $image;
        $this->updateSponsor($sponsor, $data);

        return $sponsor->wasChanged();
    }

    public function toggleSponsorStatus(Sponsor $sponsor)
    {
        $sponsor->status = !$sponsor->status;
        $sponsor->save();
    }
}
