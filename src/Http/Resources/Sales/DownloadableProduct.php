<?php

namespace Thecodebunny\PWA\Http\Resources\Sales;

use Illuminate\Http\Resources\Json\JsonResource;
use Thecodebunny\API\Http\Resources\Core\Channel as ChannelResource;
use Thecodebunny\API\Http\Resources\Customer\Customer as CustomerResource;
use Thecodebunny\API\Http\Resources\Sales\Invoice;
use Thecodebunny\API\Http\Resources\Sales\Shipment;
use Thecodebunny\API\Http\Resources\Sales\OrderAddress;

class DownloadableProduct extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'status'                => $this->status,
            'created_at'            => $this->created_at,
            'invoice_state'         => $this->invoice_state,
            'order_id'              => $this->order->increment_id,
            'remaining_downloads'   => $this->download_bought - $this->download_used,
            'title'                 => ($this->status == 'pending'
                                       || $this->status == 'expired')
                                       ? $this->product_name
                                       : $this->product_name . ' ' . '<a href="' . route('customer.downloadable_products.download', $this->id) . '" target="_blank" style="display:block;" data-class="download-link">' . $this->name . '</a>',
        ];
    }
}