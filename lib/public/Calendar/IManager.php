<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2017 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCP\Calendar;

/**
 * This class provides access to the Nextcloud CalDAV backend.
 * Use this class exclusively if you want to access calendars.
 *
 * Events/Journals/Todos in general will be expressed as an array of key-value-pairs.
 * The keys will match the property names defined in https://tools.ietf.org/html/rfc5545
 *
 * [
 *   'id' => 123,
 *   'type' => 'VEVENT',
 *   'calendar-key' => 42,
 *   'objects' => [
 *     [
 *       'SUMMARY' => ['FooBar', []],
 *       'DTSTART' => ['20171001T123456', ['TZID' => 'EUROPE/BERLIN']],
 *       'DURATION' => ['P1D', []],
 * 	     'ATTENDEE' => [
 *         ['mailto:bla@blub.com', ['CN' => 'Mr. Bla Blub']]
 *       ],
 *       'VALARM' => [
 * 	       [
 *           'TRIGGER' => ['19980101T050000Z', ['VALUE' => DATE-TIME]]
 *         ]
 *       ]
 *     ],
 *   ]
 * ]
 *
 * @since 13.0.0
 */
interface IManager {
	/**
	 * This function is used to search and find objects within the user's calendars.
	 * In case $pattern is empty all events/journals/todos will be returned.
	 *
	 * @param string $pattern which should match within the $searchProperties
	 * @param array $searchProperties defines the properties within the query pattern should match
	 * @param array $options - optional parameters:
	 *                       ['timerange' => ['start' => new DateTime(...), 'end' => new DateTime(...)]]
	 * @param integer|null $limit - limit number of search results
	 * @param integer|null $offset - offset for paging of search results
	 * @return array an array of events/journals/todos which are arrays of arrays of key-value-pairs
	 * @since 13.0.0
	 * @deprecated 23.0.0 use \OCP\Calendar\IManager::searchForPrincipal
	 */
	public function search($pattern, array $searchProperties = [], array $options = [], $limit = null, $offset = null);

	/**
	 * Check if calendars are available
	 *
	 * @return bool true if enabled, false if not
	 * @since 13.0.0
	 * @deprecated 23.0.0
	 */
	public function isEnabled();

	/**
	 * Registers a calendar
	 *
	 * @param ICalendar $calendar
	 * @return void
	 * @since 13.0.0
	 * @deprecated 23.0.0 use \OCP\AppFramework\Bootstrap\IRegistrationContext::registerCalendarProvider
	 */
	public function registerCalendar(ICalendar $calendar);

	/**
	 * Unregisters a calendar
	 *
	 * @param ICalendar $calendar
	 * @return void
	 * @since 13.0.0
	 * @deprecated 23.0.0
	 */
	public function unregisterCalendar(ICalendar $calendar);

	/**
	 * In order to improve lazy loading a closure can be registered which will be called in case
	 * calendars are actually requested
	 *
	 * @param \Closure $callable
	 * @return void
	 * @since 13.0.0
	 * @deprecated 23.0.0 use \OCP\AppFramework\Bootstrap\IRegistrationContext::registerCalendarProvider
	 */
	public function register(\Closure $callable);

	/**
	 * @return ICalendar[]
	 * @since 13.0.0
	 * @deprecated 23.0.0 use \OCP\Calendar\IManager::getCalendarsForPrincipal
	 */
	public function getCalendars();

	/**
	 * removes all registered calendar instances
	 *
	 * @return void
	 * @since 13.0.0
	 * @deprecated 23.0.0
	 */
	public function clear();

	/**
	 * @param string $principalUri URI of the principal
	 * @param string[] $calendarUris optionally specify which calendars to load, or all if this array is empty
	 *
	 * @return ICalendar[]
	 * @since 23.0.0
	 */
	public function getCalendarsForPrincipal(string $principalUri, array $calendarUris = []): array;

	/**
	 * Query a principals calendar(s)
	 *
	 * @param ICalendarQuery $query
	 * @return array[]
	 * @since 23.0.0
	 */
	public function searchForPrincipal(ICalendarQuery $query): array;

	/**
	 * Build a new query for searchForPrincipal
	 *
	 * @return ICalendarQuery
	 * @since 23.0.0
	 */
	public function newQuery(string $principalUri) : ICalendarQuery;

	/**
	 * Handle a iMip REQUEST message
	 *
	 * @since 31.0.0
	 */
	public function handleIMipRequest(string $principalUri, string $sender, string $recipient, string $calendarData): bool;

	/**
	 * Handle a iMip REPLY message
	 *
	 * @since 25.0.0
	 */
	public function handleIMipReply(string $principalUri, string $sender, string $recipient, string $calendarData): bool;

	/**
	 * Handle a iMip CANCEL message
	 *
	 * @since 25.0.0
	 */
	public function handleIMipCancel(string $principalUri, string $sender, ?string $replyTo, string $recipient, string $calendarData): bool;
}
