export type Dictionary = Record< string, string >;

/**
 * Allow for the entire object to be passed, with only some properties
 * required.
 */
export type LooselyMustHave< T, K extends keyof T > = Partial< T > &
	Pick< T, K >;

export type Require< T, K extends keyof T > = T & {
	[ P in K ]-?: T[ K ];
};

export type HTMLElementEvent< T extends HTMLElement > = Event & {
	target: T;
};
