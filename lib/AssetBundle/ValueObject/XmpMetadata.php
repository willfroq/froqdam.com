<?php

declare(strict_types=1);

namespace Froq\AssetBundle\ValueObject;

use Webmozart\Assert\Assert;

final class XmpMetadata
{
    public function __construct(
        public ?string $approvalsub = null,
        public ?string $artworkcreator = null,
        public ?string $calfop = null,
        public ?string $category = null,
        public ?string $client = null,
        public ?string $cutprov = null,
        public ?string $deliver = null,
        public ?string $ean = null,
        public ?string $epsonmaterial = null,
        public ?string $epsonprint = null,
        public ?string $fcg = null,
        public ?string $filename = null,
        public ?string $flow = null,
        public ?string $gmgflow = null,
        public ?string $introweek = null,
        public ?string $jobapproval = null,
        public ?string $jobdate = null,
        public ?string $jobdeliver = null,
        public ?string $jobemail = null,
        public ?string $jobepson = null,
        public ?string $jobfilecopies = null,
        public ?string $jobfilename = null,
        public ?string $jobhierarchy = null,
        public ?string $jobsubmitmetadata = null,
        public ?string $jobswitchflow = null,
        public ?string $jobuser = null,
        public ?string $jobxerox = null,
        public ?string $launchdate = null,
        public ?string $lithographer = null,
        public ?string $market = null,
        public ?string $orgcg = null,
        public ?string $plmnumber = null,
        public ?string $pocclient = null,
        public ?string $printer = null,
        public ?string $printingmaterial = null,
        public ?string $productionmanager = null,
        public ?string $productname = null,
        public ?string $producttype = null,
        public ?string $projectname = null,
        public ?string $projectnumber = null,
        public ?string $segment = null,
        public ?string $shape = null,
        public ?string $shapecode = null,
        public ?string $software = null,
        public ?string $subbrand = null,
        public ?string $subcategory = null,
        public ?string $xeroxflow = null,
    ) {
        Assert::nullOrStringNotEmpty($this->approvalsub, 'Expected "approvalsub" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->artworkcreator, 'Expected "artworkcreator" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->calfop, 'Expected "calfop" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->category, 'Expected "category" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->client, 'Expected "client" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->cutprov, 'Expected "cutprov" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->deliver, 'Expected "deliver" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->ean, 'Expected "ean" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->epsonmaterial, 'Expected "epsonmaterial" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->epsonprint, 'Expected "epsonprint" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->fcg, 'Expected "fcg" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->filename, 'Expected "filename" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->flow, 'Expected "flow" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->gmgflow, 'Expected "gmgflow" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->introweek, 'Expected "introweek" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->jobapproval, 'Expected "jobapproval" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->jobdate, 'Expected "jobdate" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->jobdeliver, 'Expected "jobdeliver" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->jobemail, 'Expected "jobemail" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->jobepson, 'Expected "jobepson" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->jobfilecopies, 'Expected "jobfilecopies" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->jobfilename, 'Expected "jobfilename" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->jobhierarchy, 'Expected "jobhierarchy" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->jobsubmitmetadata, 'Expected "jobsubmitmetadata" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->jobswitchflow, 'Expected "jobswitchflow" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->jobuser, 'Expected "jobuser" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->jobxerox, 'Expected "jobxerox" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->launchdate, 'Expected "launchdate" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->lithographer, 'Expected "lithographer" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->market, 'Expected "market" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->orgcg, 'Expected "orgcg" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->plmnumber, 'Expected "plmnumber" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->pocclient, 'Expected "pocclient" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->printer, 'Expected "printer" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->printingmaterial, 'Expected "printingmaterial" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->productionmanager, 'Expected "productionmanager" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->productname, 'Expected "productname" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->producttype, 'Expected "producttype" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->projectname, 'Expected "projectname" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->projectnumber, 'Expected "projectnumber" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->segment, 'Expected "segment" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->shape, 'Expected "shape" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->shapecode, 'Expected "shapecode" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->software, 'Expected "software" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->subbrand, 'Expected "subbrand" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->subcategory, 'Expected "subcategory" to be a string, got %s');
        Assert::nullOrStringNotEmpty($this->xeroxflow, 'Expected "xeroxflow" to be a string, got %s');
    }

    /** @return  array<string, string|null> */
    public function toArray(): array
    {
        return [
            'approvalsub' => $this->approvalsub,
            'artworkcreator' => $this->artworkcreator,
            'calfop' => $this->calfop,
            'category' => $this->category,
            'client' => $this->client,
            'cutprov' => $this->cutprov,
            'deliver' => $this->deliver,
            'ean' => $this->ean,
            'epsonmaterial' => $this->epsonmaterial,
            'epsonprint' => $this->epsonprint,
            'fcg' => $this->fcg,
            'filename' => $this->filename,
            'flow' => $this->flow,
            'gmgflow' => $this->gmgflow,
            'introweek' => $this->introweek,
            'jobapproval' => $this->jobapproval,
            'jobdate' => $this->jobdate,
            'jobdeliver' => $this->jobdeliver,
            'jobemail' => $this->jobemail,
            'jobepson' => $this->jobepson,
            'jobfilecopies' => $this->jobfilecopies,
            'jobfilename' => $this->jobfilename,
            'jobhierarchy' => $this->jobhierarchy,
            'jobsubmitmetadata' => $this->jobsubmitmetadata,
            'jobswitchflow' => $this->jobswitchflow,
            'jobuser' => $this->jobuser,
            'jobxerox' => $this->jobxerox,
            'launchdate' => $this->launchdate,
            'lithographer' => $this->lithographer,
            'market' => $this->market,
            'orgcg' => $this->orgcg,
            'plmnumber' => $this->plmnumber,
            'pocclient' => $this->pocclient,
            'printer' => $this->printer,
            'printingmaterial' => $this->printingmaterial,
            'productionmanager' => $this->productionmanager,
            'productname' => $this->productname,
            'producttype' => $this->producttype,
            'projectname' => $this->projectname,
            'projectnumber' => $this->projectnumber,
            'segment' => $this->segment,
            'shape' => $this->shape,
            'shapecode' => $this->shapecode,
            'software' => $this->software,
            'subbrand' => $this->subbrand,
            'subcategory' => $this->subcategory,
            'xeroxflow' => $this->xeroxflow,
        ];
    }
}
