<?php

namespace App\Http\Controllers;

use App\Models\ArticleNews;
use App\Models\Author;
use App\Models\BannerAds;
use App\Models\Category;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        // Category
        $categories = Category::all();

        // Carousel
        $articles = ArticleNews::with(["category"])
            ->where("is_featured", "not_featured")
            ->latest()
            ->take(3)
            ->get();

        // Latest hot news
        $featured_articles = ArticleNews::with(["category"])
            ->where("is_featured", "featured")
            ->inRandomOrder()
            ->take(3)
            ->get();

        // Author
        $authors = Author::all();

        // Banner Ads
        $bannerads = BannerAds::where("is_active", "active")
            ->where("type", "banner")
            ->inRandomOrder()
            ->first();

        // Latest For You in Entertainment featured
        $entertainment_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where("name", "Entertainment");
        })
            ->where("is_featured", "featured")
            ->inRandomOrder()
            ->first();

        // Latest For You in Entertainment not featured
        $entertainment_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where("name", "Entertainment");
        })
            ->where("is_featured", "not_featured")
            ->latest()
            ->take(6)
            ->get();

        // Latest For You in Bussiness featured
        $bussiness_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where("name", "Bussiness");
        })
            ->where("is_featured", "featured")
            ->inRandomOrder()
            ->first();

        // Latest For You in Bussiness not featured
        $bussiness_not_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where("name", "Bussiness");
        })
            ->where("is_featured", "not_featured")
            ->latest()
            ->take(6)
            ->get();

        // Latest For You in Automotive featured
        $automotive_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where("name", "Automotive");
        })
            ->where("is_featured", "featured")
            ->inRandomOrder()
            ->first();

        // Latest For You in Automotive not featured
        $automotive_not_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where("name", "Automotive");
        })
            ->where("is_featured", "not_featured")
            ->latest()
            ->take(6)
            ->get();

        return view("front.index", compact("categories", "articles", "authors", "featured_articles", "bannerads", "entertainment_articles", "entertainment_featured_articles", "bussiness_featured_articles", "bussiness_not_featured_articles", "automotive_featured_articles", "automotive_not_featured_articles"));
    }

    public function category(Category $category)
    {
        // All Categories
        $categories = Category::all();

        // Banner Ads
        $bannerads = BannerAds::where("is_active", "active")
            ->where("type", "banner")
            ->inRandomOrder()
            ->first();

        return  view("front.category", compact("category", "categories", "bannerads"));
    }

    public function author(Author $author)
    {
        // All Categories
        $categories = Category::all();

        // Banner Ads
        $bannerads = BannerAds::where("is_active", "active")
            ->where("type", "banner")
            ->inRandomOrder()
            ->first();

        return view("front.author", compact("author", "categories", "bannerads"));
    }

    public function search(Request $request)
    {
        $request->validate([
            "keyword" => ["required", "string", "max:255"],
        ]);

        // All Categories
        $categories = Category::all();

        $keyword = $request->keyword;

        $articles = ArticleNews::with(["category", "author"])
            ->where("name", "like", "%" . $keyword . "%")->paginate(6);

        return view("front.search", compact("articles", "keyword", "categories"));
    }

    public function details(ArticleNews $articleNews)
    {
        // All Categories
        $categories = Category::all();

        // view article recommendation
        $articles = ArticleNews::with(["category"])
            ->where("is_featured", "not_featured")
            ->where("id", "!=", $articleNews->id)
            ->latest()
            ->take(3)
            ->get();

        // iklan banner
        $bannerads = BannerAds::where("is_active", "active")
            ->where("type", "banner")
            ->inRandomOrder()
            ->first();

        // iklan square
        $squareads = BannerAds::where("is_active", "active")
            ->where("type", "square")
            ->inRandomOrder()
            ->take(2)
            ->get();

        if ($squareads->count() < 2) {
            $square_ads_1 = $squareads->first();
            $square_ads_2 = $squareads->first();
        } else {
            $square_ads_1 = $squareads->get(0);
            $square_ads_2 = $squareads->get(1);
        }

        // author article
        $author_news = ArticleNews::where("author_id", $articleNews->author_id)
            ->where("id", "!=", $articleNews->id)
            ->inRandomOrder()
            ->get();

        return view("front.details", compact("articleNews", "categories", "articles", "bannerads", "square_ads_1", "square_ads_2", "author_news"));
    }
}
