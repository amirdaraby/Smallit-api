<?php

namespace App\Http\Controllers;
use App\Http\Requests\UrlRequest;
use App\Models\Domain;
use App\Models\ShortUrl;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\AbstractList;
use PhpParser\Node\Expr\New_;
use function PHPUnit\Framework\isEmpty;

class UrlShorterController extends Controller
{

    public function index()
    {
        return ShortUrl::with("domain")->get();
    }

    public function FindOrNewDomain($url){
        $domain = Domain::where("url","=",$url)->first();
//        return $domain;
        if (! isset($domain) )
        {
            $domain = Domain::create([
                     "url"=>$url
                 ]);
            return $domain->id;
        }
        else return $domain->id;

    }


    public function store(UrlRequest $request)
    {
//        return $this->FindOrNewDomain($request->url);
       return ShortUrl::create([
            "url"=>"abcefg",
            "domain_id"=>$this->FindOrNewDomain($request->url),
            "user_id"=>1 // TODO change this
        ]);
    }


    public function show(ShortUrl $url)
    {

//         $domain = ShortUrl::find($url)->with("domain")->first();
            return $url->domain->url;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy(ShortUrl $url)
    {
        return ShortUrl::destroy($url->id);
    }
}
