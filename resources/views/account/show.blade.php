@extends('layouts.main')

@section('content')
        <div class="d-flex flex-column flex-sm-row flex-wrap margin-bottom-3 margin-top-3">
            <div class="col-sm-4 col-md-3 col-lg-2 pl-sm-0 d-flex justify-content-center justify-content-sm-start">
                <img src="{{ asset('storage/'.$account->profile_pic_filename) }}" class="img-responsive rounded-circle instagram-avatar" alt="{{ $account->username }}">
            </div>
            <div class="col-sm-8 col-md-9 col-lg-6 pl-sm-0 d-flex justify-content-center justify-content-sm-start">
                <div class="row d-flex flex-column">
                    <p class="m-0">
                        <a href="https://instagram.com/{{ $account->username }}" target="_blank" class="text-dark" rel="nofollow">{{ '@'.$account->username }}</a>
                    </p>
                    <div class="d-flex flex-row">
                        <h1>{{ $account->username }}</h1>
                        @if ($account->is_verified == 1)
                            <span class="align-self-center ml-3" data-toggle="tooltip" title="Instagram Verified"><i class="fa fa-fw fa-check-circle user-verified-badge"></i></span>
                        @endif
                    </div>
                    <small class="text-muted">{{ $account->biography }}</small>
                </div>
            </div>
            <div class="col-md-12 col-lg-4 d-flex justify-content-around align-items-center mt-4 mt-lg-0 pl-sm-0">
                <div class="col d-flex flex-column justify-content-center">
                    Followers
                    <p class="report-header-number">{{ number_format($account->lastStat->followers_count, 0, '.', ',')}}</p>
                </div>

                <div class="col d-flex flex-column justify-content-center">
                    Uploads
                    <p class="report-header-number">{{ number_format($account->lastStat->uploads_count, 0, '.', ',')}}</p>
                </div>

                <div class="col d-flex flex-column justify-content-center">
                    Engagement
                    <p class="report-header-number">{{ number_format($account->engagement_percentage , 2, '.', ',') }}%</p>
                </div>
            </div>
        </div>

        <div class="margin-bottom-3">

            <div class="row">
                <div class="col">
                    <h5>
                        Engagement
                        <span data-toggle="tooltip" title="The engagement rate is the number of active likes / comments on each post"><i class="fa fa-fw fa-question-circle text-muted"></i></span>
                    </h5>
                </div>

                <div class="col">
                    <span class="report-content-number">{{ number_format($account->engagement_percentage , 2, '.', ',') }}%</span>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <h5>
                        Average Likes
                        <span data-toggle="tooltip" title="Average likes based on the last 10 posts"> <i class="fa fa-fw fa-heart like-color"></i></span>
                    </h5>
                </div>

                <div class="col">
                    <span class="report-content-number">{{ number_format($account->averageStat('likes_count'), 0, '.', ',') }}</span>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <h5>
                        Average Comments
                        <span data-toggle="tooltip" title="Average comments based on the last 10 posts"><i class="fa fa-fw fa-comments text-muted"></i></span>
                    </h5>
                </div>

                <div class="col">
                    <span class="report-content-number">{{ number_format($account->averageStat('comments_count'), 0, '.', ',') }}</span>
                </div>
            </div>
        </div>


        <div class="margin-bottom-6">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-baseline align-items-md-center">
                <h2>Statistics Summary</h2>
            </div>
            <div>
                <h6 class="text-center">Followers evolution chart</h6>
                <div class="chart-container">
                    {!! $lineFollowersChart->render() !!}
                </div>
            </div>
            <div class="margin-top-3">
                <h6 class="text-center">Following evolution chart</h6>
                <div class="chart-container">
                    {!! $lineFollowingChart->render() !!}
                </div>
            </div>
        </div>


        <div class="margin-bottom-6">
            <div class="d-flex justify-content-between">
                <h2>Account Stats Summary</h2>
            </div>

            <p class="text-muted">Showing last {{ count($summaryStats) }} entries.</p>

            <table class="table table-responsive-md">
                <thead class="thead-black bg-instagram">
                <tr>
                    <th>
                        Date&nbsp;
                        <span data-toggle="tooltip" title="Format: Y-m-d"><i class="fa fa-fw fa-question-circle text-muted"></i></span>
                    </th>
                    <th></th>
                    <th>Followers</th>
                    <th></th>
                    <th>Following</th>
                    <th></th>
                    <th>Uploads</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($summaryStats as $summaryStat)
                    <tr>
                        <td>{{ $summaryStat->data_capture_date }}</td>
                        <td>{{ $summaryStat->data_capture_short_weekday }}</td>
                        <td>{{ number_format($summaryStat->followers_count, 0, '.', ',') }}</td>
                        <td>
                            @if ($summaryStat->followers_count_change > 0)
                                <span style="color: #28a745 !important;">+{{ number_format($summaryStat->followers_count_change, 0, '.', ',') }}</span>
                            @elseif ($summaryStat->followers_count_change < 0)
                                <span style="color: #dc3545 !important;">{{  number_format($summaryStat->followers_count_change, 0, '.', ',')  }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ number_format($summaryStat->following_count, 0, '.', ',') }}</td>
                        <td>
                            @if ($summaryStat->following_count_change > 0)
                                <span style="color: #28a745 !important;">+{{ number_format($summaryStat->following_count_change, 0, '.', ',') }}</span>
                            @elseif ($summaryStat->following_count_change < 0)
                                <span style="color: #dc3545 !important;">{{  number_format($summaryStat->following_count_change, 0, '.', ',')  }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ number_format($summaryStat->uploads_count, 0, '.', ',') }}</td>
                        <td>
                            @if ($summaryStat->uploads_count_change > 0)
                                <span style="color: #28a745 !important;">+{{ number_format($summaryStat->uploads_count_change, 0, '.', ',') }}</span>
                            @elseif ($summaryStat->uploads_count_change < 0)
                                <span style="color: #dc3545 !important;">{{  number_format($summaryStat->uploads_count_change, 0, '.', ',')  }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr class="bg-light">
                    <td colspan="2"><i class="fa fa-fw fa-list-ol"></i> Total Summary</td>
                    <td colspan="2">
                        @php ($followers_count_change_sum = $summaryStats->sum('followers_count_change'))
                        @if ($followers_count_change_sum > 0)
                            <span style="color: #28a745 !important;">+{{ number_format($followers_count_change_sum, 0, '.', ',') }}</span>
                        @elseif ($followers_count_change_sum < 0)
                            <span style="color: #dc3545 !important;">{{  number_format($followers_count_change_sum, 0, '.', ',')  }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td colspan="2">
                        @php ($following_count_change_sum = $summaryStats->sum('following_count_change'))
                        @if ($following_count_change_sum > 0)
                            <span style="color: #28a745 !important;">+{{ number_format($following_count_change_sum, 0, '.', ',') }}</span>
                        @elseif ($following_count_change_sum < 0)
                            <span style="color: #dc3545 !important;">{{  number_format($following_count_change_sum, 0, '.', ',')  }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td colspan="2">
                        @php ($uploads_count_change_sum = $summaryStats->sum('uploads_count_change'))
                        @if ($uploads_count_change_sum > 0)
                            <span style="color: #28a745 !important;">+{{ number_format($uploads_count_change_sum, 0, '.', ',') }}</span>
                        @elseif ($uploads_count_change_sum < 0)
                            <span style="color: #dc3545 !important;">{{  number_format($uploads_count_change_sum, 0, '.', ',')  }}</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="margin-bottom-6">
            <h2>Average Engagement Rate Chart</h2>
            <p class="text-muted">Each value in this chart is equal to the Average Engagement Rate of the account in that specific day.</p>

            <div>
                <h6 class="text-center">Average Engagement Rate</h6>
                <div class="chart-container">
                    {!! $lineEngagementChart->render() !!}
                </div>
            </div>


        </div>

        <div class="margin-bottom-6">
            <h2>Future Projections</h2>
            <p class="text-muted">Here you can see the approximated future projections based on your previous days averages</p>

            <table class="table table-responsive-md">
                <thead class="thead-black">
                <tr>
                    <th>Time Until</th>
                    <th>Date</th>
                    <th>Followers</th>
                    <th>Uploads</th>
                </tr>
                </thead>

                <tbody>
                <tr class="bg-light">
                    <td>Current Stats</td>
                    <td>{{ $account->data_capture_date }}</td>
                    <td>{{ $account->lastStat->followers_count}}</td>
                    <td>{{ $account->lastStat->uploads_count}}</td>
                </tr>
                @foreach ($projectionTableData as $label => $numberOfDays)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ date('Y-m-d', strtotime($account->data_capture_date . " +{$numberOfDays} days")) }}</td>
                    <td>{{ number_format(max($account->lastStat->followers_count + $account->getAverageStatPerDayChange('followers_count_change') * $numberOfDays, 0), 0, '.', ',') }}</td>
                    <td>{{ number_format(max($account->lastStat->uploads_count + $account->getAverageStatPerDayChange('uploads_count_change') * $numberOfDays, 0), 0, '.', ',') }}</td>
                </tr>
                @endforeach

                <tr class="bg-light">
                    <td colspan="2"><i class="fa fa-fw fa-calculator"></i> Based on an average of</td>
                    <td>
                        @php ($average_followers_count_change_per_day = $account->getAverageStatPerDayChange('followers_count_change'))
                        @if ($average_followers_count_change_per_day > 0)
                            <span style="color: #28a745 !important;">+{{ number_format($average_followers_count_change_per_day, 0, '.', ',') }}</span>
                        @elseif ($average_followers_count_change_per_day < 0)
                            <span style="color: #dc3545 !important;">{{  number_format($average_followers_count_change_per_day, 0, '.', ',')  }}</span>
                        @else
                            -
                        @endif
                        followers /day
                    </td>
                    <td>
                        @php ($average_uploads_count_change_per_day = $account->getAverageStatPerDayChange('uploads_count_change'))
                        @if ($average_uploads_count_change_per_day > 0)
                            <span style="color: #28a745 !important;">+{{ number_format($average_uploads_count_change_per_day, 0, '.', ',') }}</span>
                        @elseif ($average_uploads_count_change_per_day < 0)
                            <span style="color: #dc3545 !important;">{{ number_format($average_uploads_count_change_per_day, 0, '.', ',')  }}</span>
                        @else
                            -
                        @endif
                        uploads /day
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="margin-bottom-6 d-print-none">

            <h2>Top Posts</h2>
            <p class="text-muted">Top posts from the last 10 posts</p>

            <div class="row mb-5">
                @foreach(collect($latestPosts)->sortByDesc('likes_count')->values()->take(3) as $post)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/p/{{ $post['shortcode'] }}/?utm_source=ig_embed&amp;utm_campaign=loading" data-instgrm-version="14" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:450px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:16px;"> <a href="https://www.instagram.com/p/B_qh-EYnrjW/?utm_source=ig_embed&amp;utm_campaign=loading" style=" background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;" target="_blank"> <div style=" display: flex; flex-direction: row; align-items: center;"> <div style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;"></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;"></div></div></div><div style="padding: 19% 0;"></div> <div style="display:block; height:50px; margin:0 auto 12px; width:50px;"><svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-511.000000, -20.000000)" fill="#000000"><g><path d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.755,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.755,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631"></path></g></g></g></svg></div><div style="padding-top: 8px;"> <div style=" color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;">View this post on Instagram</div></div><div style="padding: 12.5% 0;"></div> <div style="display: flex; flex-direction: row; margin-bottom: 14px; align-items: center;"><div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(0px) translateY(7px);"></div> <div style="background-color: #F4F4F4; height: 12.5px; transform: rotate(-45deg) translateX(3px) translateY(1px); width: 12.5px; flex-grow: 0; margin-right: 14px; margin-left: 2px;"></div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(9px) translateY(-18px);"></div></div><div style="margin-left: 8px;"> <div style=" background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 20px; width: 20px;"></div> <div style=" width: 0; height: 0; border-top: 2px solid transparent; border-left: 6px solid #f4f4f4; border-bottom: 2px solid transparent; transform: translateX(16px) translateY(-4px) rotate(30deg)"></div></div><div style="margin-left: auto;"> <div style=" width: 0px; border-top: 8px solid #F4F4F4; border-right: 8px solid transparent; transform: translateY(16px);"></div> <div style=" background-color: #F4F4F4; flex-grow: 0; height: 12px; width: 16px; transform: translateY(-4px);"></div> <div style=" width: 0; height: 0; border-top: 8px solid #F4F4F4; border-left: 8px solid transparent; transform: translateY(-4px) translateX(8px);"></div></div></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center; margin-bottom: 24px;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 224px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 144px;"></div></div></a><p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;"><a href="https://www.instagram.com/p/{{ $post['shortcode'] }}/?utm_source=ig_embed&amp;utm_campaign=loading" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none;" target="_blank">A post shared by {{ $account->fullname }} ({{ '@'.$account->username }})</a></p></div></blockquote>
                </div>
                @endforeach
                <script async src="//www.instagram.com/embed.js"></script>
            </div>
        </div>


        <div class="margin-bottom-6">
            <div class="row">
                <div class="col">
                    <h2>Top @Mentions</h2>
                    <p class="text-muted">Top mentions from the last 10 posts</p>

                    <div class="d-flex flex-column">
                        @foreach($mentions as $mention => $count)
                            <div class="d-flex align-items-center">
                                <a href="https://www.instagram.com/{{ $mention }}" class="text-dark report-content-number-link" target="_blank">{{ $mention }}</a>
                                <span class="report-content-number" data-toggle="tooltip" title="Used in {{ $count }} out of 10 posts">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col">
                    <h2>Top #Hashtags</h2>
                    <p class="text-muted">Top hashtags from the last 10 posts</p>
                    <div class="d-flex flex-column">
                        @foreach($hashtags as $hashtag => $count)
                            <div class="d-flex align-items-center">
                                <a href="https://www.instagram.com/explore/tags/{{ $hashtag }}/" class="text-dark report-content-number-link" target="_blank">{{ $hashtag }}</a>
                                <span class="report-content-number" data-toggle="tooltip" title="Used in {{ $count }} out of 10 posts">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="margin-bottom-6">
            <h2>Media Stats Summary</h2>
            <p class="text-muted">Showing last 10 entries.</p>

            <table class="table table-responsive-md">
                <thead class="thead-black">
                <tr>
                    <th></th>
                    <th></th>
                    <th>Posted On</th>
                    <th>Caption</th>
                    <th>Likes</th>
                    <th>Comments</th>
                </tr>
                </thead>
                <tbody>
                @foreach($latestPosts as $post)
                <tr>
                    <td>
                        <a href="{{ $post['link'] }}" target="_blank">
                            @switch($post['type'])
                                @case('video')
                                <i class="fa fa-fw fa-video"></i>
                                @break

                                @case('image')
                                <i class="fa fa-fw fa-image"></i>
                                @break

                                @case('sidecar')
                                <i class="fa fa-fw fa-images"></i>
                                @break
                            @endswitch
                        </a>
                    </td>
                    <td><img src="{{ asset('storage/'.$account->profile_pic_filename) }}" class="img-responsive rounded-circle instagram-avatar-small"></td>
                    <td><span>{{ date('Y-m-d', strtotime($post['external_created_at'])) }}</span></td>
                    <td><span data-toggle="tooltip" title="{{ $post['caption'] }}">{{ \Illuminate\Support\Str::limit($post['caption'], 30, $end='...') }}</span></td>
                    <td>
                        <i class="fa fa-fw fa-heart like-color"></i>
                        {{ number_format($post['likes_count'] , 0, '.', ',') }}
                        @if ($account->averageStat('likes_count'))
                            @php ($likes_change_percentage = (($post['likes_count'] - $account->averageStat('likes_count'))/$account->averageStat('likes_count')) * 100 )
                            @if ($likes_change_percentage > 0)
                                <span style="color: #28a745 !important;">+{{ number_format($likes_change_percentage, 0, '.', ',') }}%</span>
                            @elseif ($likes_change_percentage < 0)
                                <span style="color: #dc3545 !important;">{{ number_format($likes_change_percentage, 0, '.', ',')  }}%</span>
                            @else
                                0%
                            @endif
                        @endif
                    </td>
                    <td>
                        <i class="fa fa-fw fa-comments"></i>
                        {{ number_format($post['comments_count'] , 0, '.', ',') }}
                        @if($account->averageStat('comments_count'))
                            @php ($comments_change_percentage = (($post['comments_count'] - $account->averageStat('comments_count'))/$account->averageStat('comments_count')) * 100 )
                            @if ($comments_change_percentage > 0)
                                <span style="color: #28a745 !important;">+{{ number_format($comments_change_percentage, 0, '.', ',') }}%</span>
                            @elseif ($comments_change_percentage < 0)
                                <span style="color: #dc3545 !important;">{{ number_format($comments_change_percentage, 0, '.', ',')  }}%</span>
                            @else
                                0%
                            @endif
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="margin-bottom-6">
            <h2>Media Stats Chart</h2>

            <div class="chart-container">
                {!! $mediaStatLineChart->render() !!}
            </div>

        </div>
@stop

