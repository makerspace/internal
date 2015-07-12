#!/usr/bin/python

import common
import datetime
import random

def make_unique_rfid(db, length):
    rfid = common.get_random_digit_string(length).zfill(16)
    while not common.ensure_unique_value(db, 'member_rfid', 'tagid', rfid):
        print "RFID collision: randomized tagid already in database: %s. Randomizing again, no worries." % (rfid)
        rfid = common.get_random_digit_string(length).zfill(16)
    return rfid

class MemberRFID:
    created_at = None
    updated_at = None
    member_id = None
    active = None
    tagid = None
    description = None

def generate(db, report, members):
    print "Populating table: member_rfid"

    descriptions = ["Work tag", "Surgically implanted in hand", "Cell phone chip", "Sewn into wallet", "Of unknown extra-terrestrial origin"]

    # At least one member per call should have an inactive tag
    inactive_tag_index = random.randint(0, len(members)-1)

    # At least one member per call should have three rfid tags
    three_tag_index = random.randint(0, len(members)-1)

    # At least one member per call should lack tags completely
    no_tag_index = common.random_index_with_exclude(len(members), [inactive_tag_index, three_tag_index]) 

    # At least one member per call should have a full length rfid
    full_rfid_index = common.random_index_with_exclude(len(members), [no_tag_index])

    i = 0
    for member_id in members:
        member = members[member_id]

        force_three_tags = (i == three_tag_index)
        force_no_tag = (i == no_tag_index)
        force_full_rfid = (i == full_rfid_index)
        force_inactive_tag = (i == inactive_tag_index)

        if force_three_tags:
            number_of_ids = 3
        elif force_full_rfid:
            number_of_ids = 2
        elif force_no_tag or common.chance(0.1) and not force_inactive_tag:
            number_of_ids = 0
        else:
            number_of_ids = int(1 + 3*random.random()**5) # about 20% with two keys, 8% with three keys

        if number_of_ids > 0:
            for j in range(number_of_ids):
                has_description = j > 0 or common.chance(0.1)
                if force_full_rfid and j == 1 or common.chance(0.02):
                    rfid = make_unique_rfid(db, 16)
                else:
                    rfid = make_unique_rfid(db, 8)

                tag = MemberRFID()
                tag.created_at = common.get_random_datetime(member.created_at, datetime.datetime.now())
                tag.updated_at = common.get_random_datetime(tag.created_at, datetime.datetime.now())
                tag.member_id = member_id
                tag.active = common.chance(0.9) and not force_inactive_tag
                tag.tagid = rfid
                tag.description = None if not has_description else random.choice(descriptions)
                common.insert_into_table(db, 'member_rfid', tag)

        if force_three_tags:
            report.append(("Three RFID keys", member))
        if force_no_tag:
            report.append(("No RFID key", member))
        if force_full_rfid :
            report.append(("Full RFID", member))
        if force_inactive_tag :
            report.append(("Inactive RFID tag", member))

        i = i + 1
