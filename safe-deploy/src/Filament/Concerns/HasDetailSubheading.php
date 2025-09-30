<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Concerns;

use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use SafeDeploy\Laravel\Models\User;
use URL;

/**
 * @mixin EditRecord|ViewRecord
 */
trait HasDetailSubheading
{
    public function getSubheading(): null|Htmlable|string
    {
        /**
         * @var Model&object{
         *     created_at: ?Carbon,
         *     updated_at: ?Carbon,
         *     createdBy: ?User,
         *     updatedBy: ?User
         * } $record
         */
        $record = $this->getRecord();

        $data = [
            'id' => $record->getKey(),
            'createdSince' => $record->created_at?->diffForHumans() ?? '-',
        ];

        if ($record->created_at?->toDateTimeString() !== $record->updated_at?->toDateTimeString()) {
            $data['updatedSince'] = $record->updated_at?->diffForHumans() ?? '-';
        }

        if ($record->createdBy) {
            $data += [
                'createdBy' => $record->createdBy->name,
                'createdByUrl' => URL::current(),
                //                'createdByUrl' => UserResource::getUrl(name: 'view', parameters: ['record' => $record->createdBy]),
            ];
        }

        if ($record->updatedBy) {
            $data += [
                'updatedBy' => $record->updatedBy->name,
                'updatedByUrl' => URL::current(),
                //                'updatedByUrl' => UserResource::getUrl(name: 'view', parameters: ['record' => $record->updatedBy]),
            ];
        }

        // @todo Check if the current user can view the user resource, if not, remove the link
        return new HtmlString((string) view('safe-deploy::filament.resources.pages.view.subheading', $data));
    }
}
