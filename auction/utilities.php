<?php

function is_expired($end_time){
  $now = new DateTime();
  $end_time_formatted = date_create($end_time);
  if ($now > $end_time_formatted) {
    return True;
  }
  return False;
}


// display_time_remaining:
// Helper function to help figure out what time to display
function display_time_remaining($end_time) {

    if (is_expired($end_time)) {
      return 'This auction has ended';
    }
    else {
      $now = new DateTime();
      $end_time_formatted = date_create($end_time);
      $time_to_end = date_diff($now, $end_time_formatted);
      if ($time_to_end->days == 0 && $time_to_end->h == 0) {
        // Less than one hour remaining: print mins + seconds:
        $time_remaining = $time_to_end->format('%im %Ss');
      }
      else if ($time_to_end->days == 0) {
        // Less than one day remaining: print hrs + mins:
        $time_remaining = $time_to_end->format('%hh %im');
      }
      else {
        // At least one day remaining: print days + hrs:
        $time_remaining = $time_to_end->format('%ad %hh');
      }
      $time_remaining = $time_remaining . ' remaining';
      return $time_remaining;
    }
}

// print_listing_li:
// This function prints an HTML <li> element containing an auction listing
function print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time)
{
  // Truncate long descriptions
  if (strlen($desc) > 250) {
    $desc_shortened = substr($desc, 0, 250) . '...';
  }
  else {
    $desc_shortened = $desc;
  }
  
  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  }
  else {
    $bid = ' bids';
  }
  
  $time_remaining = display_time_remaining($end_time);
  
  // Print HTML
  echo('
    <li class="list-group-item d-flex justify-content-between">
    <div class="p-2 mr-5"><h5><a href="listing.php?item_id=' . $item_id . '">' . $title . '</a></h5>' . $desc_shortened . '</div>
    <div class="text-center text-nowrap"><span style="font-size: 1.5em">£' . number_format($price, 0) . '</span><br/>' . $num_bids . $bid . '<br/>' . $time_remaining . '</div>
  </li>'
  );
}

?>